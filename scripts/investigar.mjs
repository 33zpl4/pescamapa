// Reads public web pages for editorial/competitor research and writes plain text the sandbox can read.
// Run by .github/workflows/investigar.yml (GitHub runners have open internet; the Claude sandbox does not).
// Courtesy rules: identifies itself, honours robots.txt, one request per host every ~1.5 s, a cap on pages,
// no logins, no form posts. Read-only towards every site.
//
// env: URLS (whitespace/newline separated), LABEL, SITEMAP=true|false, RENDER=true|false, OUT (output dir)

import { mkdir, writeFile } from 'node:fs/promises';
import { load } from 'cheerio';

const UA = 'ResearchBot/1.0 (editorial research; 1 request/1.5 s; obeys robots.txt)';
const MAX_URLS = 30;
const urls = (process.env.URLS || '').split(/\s+/).filter(Boolean).slice(0, MAX_URLS);
const label = (process.env.LABEL || 'investigacion').replace(/[^a-z0-9-]+/gi, '-').toLowerCase();
const wantSitemap = process.env.SITEMAP === 'true';
const render = process.env.RENDER === 'true';
const out = process.env.OUT || `research/${label}`;
await mkdir(out, { recursive: true });

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));
const lastHit = new Map();
async function polite(host) {
  const wait = (lastHit.get(host) || 0) + 1500 - Date.now();
  if (wait > 0) await sleep(wait);
  lastHit.set(host, Date.now());
}

async function get(url, accept = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.5') {
  await polite(new URL(url).host);
  const ctl = AbortSignal.timeout(25000);
  const r = await fetch(url, { headers: { 'user-agent': UA, accept, 'accept-language': 'es-AR,es;q=0.9,pt;q=0.8,en;q=0.7' }, redirect: 'follow', signal: ctl });
  const buf = Buffer.from(await r.arrayBuffer());
  return { status: r.status, finalUrl: r.url, type: r.headers.get('content-type') || '', text: buf.subarray(0, 4_000_000).toString('utf8') };
}

// --- robots.txt (simple, conservative: our UA group, else '*') ---
const robotsCache = new Map();
async function robots(origin) {
  if (robotsCache.has(origin)) return robotsCache.get(origin);
  let rules = { disallow: [], allow: [], sitemaps: [], found: false };
  try {
    const r = await get(origin + '/robots.txt', 'text/plain');
    if (r.status === 200 && !/html/i.test(r.type)) {
      rules.found = true;
      let group = null; let mine = null; let star = null;
      for (const raw of r.text.split(/\r?\n/)) {
        const line = raw.replace(/#.*/, '').trim();
        const m = line.match(/^([a-z-]+)\s*:\s*(.*)$/i);
        if (!m) continue;
        const k = m[1].toLowerCase(); const v = m[2].trim();
        if (k === 'user-agent') {
          group = v.toLowerCase();
          if (group === '*') star ??= { disallow: [], allow: [] };
          else if (UA.toLowerCase().startsWith(group.split('/')[0])) mine ??= { disallow: [], allow: [] };
        } else if (k === 'sitemap') rules.sitemaps.push(v);
        else if (k === 'disallow' || k === 'allow') {
          const target = group === '*' ? star : (mine && UA.toLowerCase().startsWith((group || '').split('/')[0]) ? mine : null);
          if (target && v) target[k].push(v);
        }
      }
      const g = mine || star || { disallow: [], allow: [] };
      rules.disallow = g.disallow; rules.allow = g.allow;
    }
  } catch { /* no robots.txt reachable: treat as none */ }
  robotsCache.set(origin, rules);
  return rules;
}
function allowed(rules, path) {
  const toRe = (p) => new RegExp('^' + p.replace(/[.+?^${}()|[\]\\]/g, '\\$&').replace(/\*/g, '.*').replace(/\\\$$/, '$'));
  const hit = (list) => list.filter((p) => toRe(p).test(path)).sort((a, b) => b.length - a.length)[0];
  const d = hit(rules.disallow); const a = hit(rules.allow);
  return !d || (a && a.length >= d.length);
}

// --- page report ---
const slug = (u) => (new URL(u).host + new URL(u).pathname).replace(/[^a-z0-9]+/gi, '-').replace(/^-|-$/g, '').slice(0, 80) || 'inicio';
const clip = (s, n) => (s.length > n ? s.slice(0, n) + '…' : s);

function analyse(html, url) {
  const $ = load(html);
  const host = new URL(url).host;
  const jsonld = [];
  $('script[type="application/ld+json"]').each((_, el) => {
    const raw = $(el).contents().text().trim();
    try {
      const j = JSON.parse(raw);
      const walk = (n) => Array.isArray(n) ? n.flatMap(walk) : n && typeof n === 'object' ? [n, ...(n['@graph'] ? walk(n['@graph']) : [])] : [];
      walk(j).forEach((n) => n['@type'] && jsonld.push({ type: [].concat(n['@type']).join('/'), name: n.name || '', raw: clip(JSON.stringify(n), 600) }));
    } catch { jsonld.push({ type: '(JSON-LD that does not parse)', name: '', raw: clip(raw, 200) }); }
  });
  const hreflang = $('link[rel="alternate"][hreflang]').map((_, e) => `${$(e).attr('hreflang')} → ${$(e).attr('href')}`).get();
  const links = $('a[href]').map((_, e) => $(e).attr('href')).get();
  let internal = 0; let external = 0; const sections = {};
  for (const h of links) {
    try { const u = new URL(h, url); if (!/^https?:$/.test(u.protocol)) continue;
      if (u.host === host) { internal++; const s = '/' + (u.pathname.split('/')[1] || ''); sections[s] = (sections[s] || 0) + 1; } else external++; } catch {}
  }
  $('script,style,noscript,svg,iframe').remove();
  $('h1,h2,h3,h4,h5,h6,p,li,div,section,article,tr,br,a,button').after('\n');
  const headings = $('h1,h2,h3').map((_, e) => `${e.tagName.toUpperCase()}: ${$(e).text().replace(/\s+/g, ' ').trim()}`).get().filter((h) => h.length > 4);
  const body = $('main').length ? $('main') : $('body');
  const text = body.text().replace(/[ \t]+/g, ' ').replace(/\s*\n\s*/g, '\n').trim();
  return {
    title: $('title').first().text().trim(),
    description: $('meta[name="description"]').attr('content') || '',
    canonical: $('link[rel="canonical"]').attr('href') || '',
    robotsMeta: $('meta[name="robots"]').attr('content') || '',
    lang: $('html').attr('lang') || '',
    og: { title: $('meta[property="og:title"]').attr('content') || '', image: $('meta[property="og:image"]').attr('content') || '' },
    hreflang, jsonld, headings, internal, external,
    sections: Object.entries(sections).sort((a, b) => b[1] - a[1]).slice(0, 12),
    images: $('img').length, words: text.split(/\s+/).filter(Boolean).length, text,
  };
}

function md(url, r, a, extra = '') {
  return [
    `# ${a.title || '(sin título)'}`, '',
    `- URL pedida: ${url}`, `- URL final: ${r.finalUrl}  (HTTP ${r.status})${extra}`,
    `- Canonical: ${a.canonical || '—'}   · lang: ${a.lang || '—'}   · robots meta: ${a.robotsMeta || '—'}`,
    `- Meta description (${a.description.length} car.): ${a.description || '—'}`,
    `- og:title: ${a.og.title || '—'}   · og:image: ${a.og.image || '—'}`,
    `- Palabras: ${a.words} · imágenes: ${a.images} · enlaces internos: ${a.internal} · externos: ${a.external}`,
    a.sections.length ? `- Secciones más enlazadas: ${a.sections.map(([s, n]) => `${s} (${n})`).join(', ')}` : '',
    a.hreflang.length ? `- hreflang:\n${a.hreflang.map((h) => '  - ' + h).join('\n')}` : '- hreflang: ninguno',
    a.jsonld.length ? `- Datos estructurados:\n${a.jsonld.map((j) => `  - ${j.type}${j.name ? ' · ' + j.name : ''}\n    ${j.raw}`).join('\n')}` : '- Datos estructurados: ninguno',
    '', '## Encabezados', ...a.headings.slice(0, 80), '', '## Texto (primeras 12.000 caracteres)', '', clip(a.text, 12000), '',
  ].filter((l) => l !== '').join('\n');
}

// --- sitemap summary ---
async function sitemapSummary(origin, rules) {
  const start = rules.sitemaps.length ? rules.sitemaps : [origin + '/sitemap.xml', origin + '/sitemap_index.xml'];
  const seen = new Set(); const pages = []; const queue = [...start]; let files = 0;
  while (queue.length && files < 15) {
    const u = queue.shift(); if (seen.has(u)) continue; seen.add(u);
    try {
      const r = await get(u, 'application/xml,text/xml,*/*');
      if (r.status !== 200) continue; files++;
      const $ = load(r.text, { xmlMode: true });
      $('sitemap > loc').each((_, e) => queue.push($(e).text().trim()));
      $('url').each((_, e) => pages.push({ loc: $(e).children('loc').text().trim(), lastmod: $(e).children('lastmod').text().trim().slice(0, 10) }));
    } catch {}
  }
  if (!pages.length) return `# Sitemap de ${origin}\n\nNo se encontró un sitemap legible.\n`;
  const groups = {};
  for (const p of pages) { try { const segs = new URL(p.loc).pathname.split('/').filter(Boolean); const g = '/' + segs.slice(0, 2).join('/'); groups[g] = (groups[g] || 0) + 1; } catch {} }
  const mods = pages.map((p) => p.lastmod).filter(Boolean).sort();
  const g20 = Object.entries(groups).sort((a, b) => b[1] - a[1]).slice(0, 25);
  return [`# Sitemap de ${origin}`, '', `- Archivos leídos: ${files} · URLs: ${pages.length}`, `- lastmod: ${mods[0] || '—'} → ${mods.at(-1) || '—'} (${mods.length} con fecha)`, '', '## URLs por primeros dos tramos', ...g20.map(([g, n]) => `- ${g}: ${n}`), '', '## Muestra', ...pages.slice(0, 20).map((p) => `- ${p.loc} ${p.lastmod}`), ''].join('\n');
}

// --- run ---
let browser = null;
if (render) { const { chromium } = await import('playwright'); browser = await chromium.launch(); }
const index = []; const origins = new Set(); let n = 0;
for (const url of urls) {
  n++;
  const id = String(n).padStart(2, '0') + '-' + slug(url);
  let origin; try { origin = new URL(url).origin; } catch { index.push({ url, note: 'URL inválida' }); continue; }
  origins.add(origin);
  const rules = await robots(origin);
  if (!allowed(rules, new URL(url).pathname + new URL(url).search)) { index.push({ url, note: 'robots.txt lo desautoriza: no se leyó' }); console.log('robots', url); continue; }
  try {
    let r; let extra = '';
    if (render) {
      await polite(new URL(url).host);
      const page = await browser.newPage({ viewport: { width: 1366, height: 900 }, userAgent: UA });
      const resp = await page.goto(url, { waitUntil: 'networkidle', timeout: 60000 }).catch(() => null);
      await page.waitForTimeout(800);
      r = { status: resp ? resp.status() : 0, finalUrl: page.url(), type: 'text/html', text: await page.content() };
      await page.screenshot({ path: `${out}/${id}.png`, fullPage: true }).catch(() => {});
      await page.close(); extra = '  · renderizada con navegador';
    } else r = await get(url);
    if (!/html|xml/i.test(r.type) && r.status === 200) { await writeFile(`${out}/${id}.txt`, `(${r.type}, no es HTML)\n` + clip(r.text, 20000)); index.push({ url, status: r.status, note: r.type }); continue; }
    const a = analyse(r.text, r.finalUrl);
    await writeFile(`${out}/${id}.md`, md(url, r, a, extra));
    await writeFile(`${out}/${id}.html`, r.text.slice(0, 1_500_000));
    index.push({ url, status: r.status, title: a.title, h1: (a.headings.find((h) => h.startsWith('H1')) || '').slice(4), words: a.words, ld: [...new Set(a.jsonld.map((j) => j.type))].join(', '), file: id + '.md' });
    console.log('✓', r.status, url, a.words, 'palabras');
  } catch (e) { index.push({ url, note: 'error: ' + e.message }); console.log('✗', url, e.message); }
}
if (wantSitemap) for (const o of origins) {
  const rules = await robots(o);
  await writeFile(`${out}/sitemap-${new URL(o).host.replace(/[^a-z0-9.-]/gi, '_')}.md`, await sitemapSummary(o, rules));
  console.log('sitemap', o);
}
if (browser) await browser.close();

await writeFile(`${out}/INDEX.md`, [
  `# ${label}: ${new Date().toISOString().slice(0, 10)}`, '',
  `Agente: ${UA}`, `Páginas pedidas: ${urls.length} · modo: ${render ? 'navegador' : 'HTTP simple'} · robots.txt respetado`, '',
  '| URL | HTTP | Título | H1 | Palabras | Datos estructurados | Archivo / nota |', '| --- | --- | --- | --- | --- | --- | --- |',
  ...index.map((i) => `| ${i.url} | ${i.status ?? ''} | ${(i.title || '').replace(/\|/g, '/')} | ${(i.h1 || '').replace(/\|/g, '/')} | ${i.words ?? ''} | ${i.ld || ''} | ${i.file || i.note || ''} |`), '',
].join('\n'));
console.log('listo:', out);
