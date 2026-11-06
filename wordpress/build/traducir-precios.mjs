#!/usr/bin/env node
// Traduce wordpress/contenido/precios/paginas.es.json a en y pt con la API de OpenAI y verifica que los números
// (y los marcadores {…}) no cambien. Salida: paginas.en.json / paginas.pt.json. Uso: node wordpress/build/traducir-precios.mjs
import { execFileSync } from 'node:child_process';
import { readFileSync, writeFileSync } from 'node:fs';
const dir = new URL('../contenido/precios/', import.meta.url).pathname;
const es = JSON.parse(readFileSync(dir + 'paginas.es.json', 'utf8'));
const L = { en: 'English (natural for US/UK anglers; write money as "US$ 7,950" with a comma as thousands separator and ARS as "$ 666,000"; dates like "5 November 2026")', pt: 'Brazilian Portuguese (pt-BR); keep money as "US$ 7.950" and "$ 666.000" with dot thousands separators; dates like "5 de novembro de 2026"' };
const nums = s => (JSON.stringify(s).match(/\d+/g) || []).sort().join(',');
const marc = s => (JSON.stringify(s).match(/\{[a-z]:?[a-z0-9-]*\}/g) || []).sort().join(',');
function llamar(prompt) {
  const r = JSON.parse(execFileSync('curl', ['-sS', '-m', '300', 'https://api.openai.com/v1/chat/completions', '-H', 'Content-Type: application/json',
    '-H', `Authorization: Bearer ${process.env.OPENAI_API_KEY}`, '-d', JSON.stringify({ model: 'gpt-4.1', response_format: { type: 'json_object' }, temperature: 0.2,
      messages: [{ role: 'system', content: 'You are a precise translator for a fishing-travel website. Output only JSON.' }, { role: 'user', content: prompt }] })], { maxBuffer: 5e7 }).toString());
  return JSON.parse(r.choices[0].message.content);
}
const args = process.argv.slice(2);
const solo = (args.find(a => a.startsWith('--solo=')) || '').slice(7).split(',').filter(Boolean); // claves a (re)traducir; el resto se conserva
const idioma = args.find(a => !a.startsWith('--'));
for (const lang of (idioma ? [idioma] : ['en', 'pt'])) {
  let out = { fecha: es.fecha, cierre: null, paginas: [] };
  if (solo.length) { try { out = JSON.parse(readFileSync(`${dir}paginas.${lang}.json`, 'utf8')); } catch { /* sin archivo previo: se arma entero */ } }
  const trad = (obj) => {
    const p = `Translate every string value of this JSON from Rioplatense Spanish to ${L[lang]}. Keep the JSON structure and keys EXACTLY (keys like t, h, cab, filas, items must not change; values of key "t" are block types and must stay as is; "clave", "slug", "especie", "fecha" must stay as is, EXCEPT translate "slug" into a natural lowercase hyphenated URL slug in the target language). Preserve every number, currency amount, date digit, HTML tag, attribute and placeholder such as {n:xxx}, {d:xxx}, {viaje}, {calendario} EXACTLY. Keep clock times in 24-hour format exactly as written (08:00, 17:00), never AM/PM. Do not add or drop content. seo_title must stay <= 55 characters (count carefully) and seo_description between 100 and 150 characters (count carefully). Tone: factual, plain, voseo not applicable. JSON:\n${JSON.stringify(obj)}`;
    for (let i = 0; i < 6; i++) {
      const r = llamar(p);
      if (nums(r) === nums(obj) && marc(r) === marc(obj) && (!r.seo_title || (r.seo_title.length <= 60 && r.seo_description.length >= 70 && r.seo_description.length <= 160))) return r;
      { const A=(JSON.stringify(obj).match(/\d+/g)||[]).sort(), B=(JSON.stringify(r).match(/\d+/g)||[]).sort(); const m=A.filter(x=>{const i=B.indexOf(x); if(i<0) return true; B.splice(i,1); return false;}); console.error('faltan/cambian:',m.join(' '),'| sobran:',B.join(' ')); } console.error(`reintento ${lang}: ${(r.clave || 'cierre')} (números o marcadores no coinciden, o SEO fuera de rango)`);
    }
    throw new Error(`no se pudo traducir con números intactos: ${lang} ${obj.clave || 'cierre'}`);
  };
  if (!solo.length) out.cierre = trad({ cierre: es.cierre }).cierre;
  out.paginas = es.paginas.map(pg => {
    if (solo.length && !solo.includes(pg.clave)) return out.paginas.find(x => x.clave === pg.clave) || trad(pg);
    return trad(pg);
  });
  writeFileSync(`${dir}paginas.${lang}.json`, JSON.stringify(out, null, 1));
  console.log(`ok ${lang}: ${out.paginas.length} páginas`);
}
