#!/usr/bin/env node
// Corre las preguntas de control (docs/preguntas-llm.md) + la ancla del dorado en ChatGPT (con búsqueda) y en Gemini (con Google Search)
// y escribe respuestas + resumen en la carpeta de salida. Cada proveedor se salta con una nota si no tiene su clave.
// Uso: OUT=/ruta node scripts/medir-ia.mjs   (claves: OPENAI_API_KEY, GEMINI_API_KEY)
import { readFileSync, writeFileSync, mkdirSync } from 'node:fs';
import { execFileSync } from 'node:child_process';
const out = process.env.OUT || 'medicion-ia-salida';
const real = k => process.env[k] && process.env[k] !== 'proxy-injected';
const proveedores = [['openai', real('OPENAI_API_KEY')], ['gemini-search', real('GEMINI_API_KEY')]];
const preguntas = readFileSync(new URL('../docs/preguntas-llm.md', import.meta.url), 'utf8').split('\n')
  .map(l => l.match(/^\|\s*(\d+)\s*\|\s*(es|en|pt)\s*\|\s*(.+?)\s*\|\s*$/)).filter(Boolean).map(m => ({ n: m[1], q: m[3] }));
preguntas.push({ n: 'dorado-amigos', q: 'Quiero organizar un viaje de pesca con amigos para pescar dorado en Argentina. ¿Cuántos días conviene, cuánto cuesta por persona y dónde me alojo? Citá fuentes.' });
mkdirSync(out, { recursive: true });
const filas = [], dom = {};
for (const [prov, ok] of proveedores) {
  if (!ok) { filas.push(`- **${prov}: omitido** (falta el secreto ${prov === 'openai' ? 'OPENAI_API_KEY' : 'GEMINI_API_KEY'})`); continue; }
  let citan = 0, errores = [], total = 0;
  for (const { n, q } of preguntas) {
    const f = `${out}/${prov}-p${n}.json`;
    try { execFileSync('node', [new URL('./consultar-ia.mjs', import.meta.url).pathname, prov, q, f], { stdio: 'ignore', env: process.env, timeout: 600000 }); } catch { /* el error queda en el JSON o falta el archivo */ }
    total++;
    try {
      const r = JSON.parse(readFileSync(f, 'utf8'));
      if (r.error) errores.push(`p${n}`);
      if (r.cita_pescamapa) citan++;
      for (const u of (r.dominios?.length ? r.dominios : r.urls)) { try { const h = r.dominios?.length ? u : new URL(u).hostname.replace(/^www\./, ''); (dom[prov] ||= {})[h] = (dom[prov][h] || 0) + 1; } catch {} }
    } catch { errores.push(`p${n}`); }
  }
  const top = Object.entries(dom[prov] || {}).sort((a, b) => b[1] - a[1]).slice(0, 15).map(([h, c]) => `${h} (${c})`).join(', ');
  filas.push(`- **${prov}:** ${total} preguntas, **citan a Pescamapa: ${citan}**, con error o vacías: ${errores.length ? errores.join(', ') : 'ninguna'}.\n  - Dominios más citados: ${top || 'ninguno'}`);
}
const fecha = new Date().toISOString().slice(0, 10);
writeFileSync(`${out}/LEEME.md`, `# Medición en IA, ${fecha}\n\nPreguntas: las de \`docs/preguntas-llm.md\` + la ancla del dorado con amigos. Mismo conjunto en cada corrida para poder comparar con la línea base del 5/11/2026 (\`docs/investigacion/medicion-ia/2026-11-05/LEEME.md\`).\n\n${filas.join('\n')}\n`);
console.log(readFileSync(`${out}/LEEME.md`, 'utf8'));
