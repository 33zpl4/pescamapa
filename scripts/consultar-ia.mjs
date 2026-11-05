#!/usr/bin/env node
// Consulta a ChatGPT (Responses API + web_search) y/o Gemini (endpoint compatible, sin búsqueda)
// y guarda respuesta, URLs citadas y si aparece pescamapa.com.
// Uso: node scripts/consultar-ia.mjs <openai|gemini> "pregunta" [salida.json]
// Claves: variables OPENAI_API_KEY / GEMINI_API_KEY, o inyectadas por el proxy de la sesión (placeholder).
import { execFileSync } from 'node:child_process';
import { writeFileSync, mkdirSync } from 'node:fs';
import { dirname } from 'node:path';

const [prov, pregunta, salida] = process.argv.slice(2);
if (!prov || !pregunta) { console.error('uso: consultar-ia.mjs <openai|gemini> "pregunta" [salida.json]'); process.exit(1); }

function post(url, cuerpo, clave) {
  const args = ['-sS', '-m', '240', url, '-H', 'Content-Type: application/json', '-d', JSON.stringify(cuerpo)];
  if (clave) args.push('-H', `Authorization: Bearer ${clave}`);
  return JSON.parse(execFileSync('curl', args, { maxBuffer: 50e6 }).toString());
}
const intentar = (f, n = 4) => { for (let i = 1; ; i++) { try { const r = f(); if (!r.error || i >= n) return r; } catch (e) { if (i >= n) throw e; } execFileSync('sleep', [String(6 * i)]); } };

let texto = '', urls = [], crudo;
if (prov === 'openai') {
  crudo = intentar(() => post('https://api.openai.com/v1/responses',
    { model: process.env.OPENAI_MODEL || 'gpt-4.1', tools: [{ type: 'web_search_preview' }], input: pregunta },
    process.env.OPENAI_API_KEY));
  for (const it of crudo.output || []) if (it.type === 'message') for (const c of it.content || []) {
    texto += c.text || '';
    for (const a of c.annotations || []) if (a.url) urls.push(a.url);
  }
} else if (prov === 'gemini') {
  crudo = intentar(() => post('https://generativelanguage.googleapis.com/v1beta/openai/chat/completions',
    { model: process.env.GEMINI_MODEL || 'gemini-3.5-flash', messages: [{ role: 'user', content: pregunta }] },
    process.env.GEMINI_API_KEY));
  texto = crudo.choices?.[0]?.message?.content || '';
  urls = [...texto.matchAll(/https?:\/\/[^\s)\]>"']+/g)].map(m => m[0]);
} else { console.error('proveedor desconocido'); process.exit(1); }

urls = [...new Set(urls.map(u => u.replace(/[?&]utm_[^&]+/g, '')))];
const res = { fecha: new Date().toISOString(), proveedor: prov, pregunta, cita_pescamapa: urls.some(u => /pescamapa\.com/.test(u)) || /pescamapa/i.test(texto), urls, texto, error: crudo.error || null };
if (salida) { mkdirSync(dirname(salida), { recursive: true }); writeFileSync(salida, JSON.stringify(res, null, 2)); }
console.log(JSON.stringify({ ...res, texto: res.texto.slice(0, 400) }, null, 1));
