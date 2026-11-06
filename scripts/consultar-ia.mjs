#!/usr/bin/env node
// Consulta a ChatGPT (Responses API + web_search) y/o Gemini (endpoint compatible, sin búsqueda)
// y guarda respuesta, URLs citadas y si aparece pescamapa.com.
// Uso: node scripts/consultar-ia.mjs <openai|gemini|gemini-search> "pregunta" [salida.json]
// Claves: variables OPENAI_API_KEY / GEMINI_API_KEY, o inyectadas por el proxy de la sesión (placeholder).
import { execFileSync } from 'node:child_process';
import { writeFileSync, mkdirSync } from 'node:fs';
import { dirname } from 'node:path';

const [prov, pregunta, salida] = process.argv.slice(2);
if (!prov || !pregunta) { console.error('uso: consultar-ia.mjs <openai|gemini> "pregunta" [salida.json]'); process.exit(1); }

function post(url, cuerpo, clave) {
  const args = ['-sS', '-m', '120', url, '-H', 'Content-Type: application/json', '-d', JSON.stringify(cuerpo)];
  if (clave) args.push('-H', `Authorization: Bearer ${clave}`);
  const r = JSON.parse(execFileSync('curl', args, { maxBuffer: 50e6 }).toString());
  return Array.isArray(r) && r[0]?.error ? r[0] : r; // Gemini devuelve los errores como [{error}]
}
const vacia = r => !r.error && !(r.choices?.[0]?.message?.content) && !(r.output || []).some(o => o.type === 'message');
const intentar = (f, n = 3) => { for (let i = 1; ; i++) { try { const r = f(); if ((!r.error && !vacia(r)) || i >= n) return r; } catch (e) { if (i >= n) throw e; } execFileSync('sleep', [String(6 * i)]); } };


// Huellas de datos propios (6/11/2026): cifras que calculamos nosotros y no están en ninguna fuente pública.
// Detectarlas en una respuesta prueba que nos usaron aunque no nos citen. Se compara sin separadores de miles.
// Agregar acá cada cifra calculada nueva que publiquemos (con la página de origen).
const HUELLAS = {
  '1815': 'dorado con amigos: total de 3 amigos, 2 días (Hambaré)', '2820': 'dorado con amigos: 4 amigos, dos lanchas', '3630': 'dorado con amigos: 6 amigos, dos lanchas',
  '2565': 'dorado con amigos: total grupo de 3, 3 días', '3315': 'dorado con amigos: total grupo de 3, 4 días',
  '1783': 'precio por día de pesca, lodge Corrientes 4 noches', '1624': 'precio por día, lodge Corrientes 5 noches', '1325': 'precio por día, lodge Corrientes 7 noches',
  '1438': 'precio por día, lodge Chimehuín 5 noches', '1158': 'precio por día, lodge Chimehuín 7 noches',
};
const sinSeparadores = t => t.replace(/(\d)[.,\u00a0 ](?=\d{3}\b)/g, '$1');
const detectarHuellas = t => { const n = sinSeparadores(t); return Object.keys(HUELLAS).filter(h => new RegExp(`(?<![\\d])${h}(?![\\d])`).test(n)); };

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
    { model: process.env.GEMINI_MODEL || 'gemini-3.5-flash', max_tokens: 8000, messages: [{ role: 'user', content: pregunta }] },
    process.env.GEMINI_API_KEY));
  texto = crudo.choices?.[0]?.message?.content || '';
  urls = [...texto.matchAll(/https?:\/\/[^\s)\]>"']+/g)].map(m => m[0]);
} else if (prov === 'gemini-search') {
  // Endpoint nativo con Google Search (grounding): exige la clave real en x-goog-api-key (secreto GEMINI_API_KEY en Actions).
  if (!process.env.GEMINI_API_KEY || process.env.GEMINI_API_KEY === 'proxy-injected') { console.error('gemini-search necesita GEMINI_API_KEY real'); process.exit(2); }
  const modelo = process.env.GEMINI_MODEL || 'gemini-3.5-flash';
  crudo = intentar(() => {
    const r = JSON.parse(execFileSync('curl', ['-sS', '-m', '120', `https://generativelanguage.googleapis.com/v1beta/models/${modelo}:generateContent`,
      '-H', 'Content-Type: application/json', '-H', `x-goog-api-key: ${process.env.GEMINI_API_KEY}`,
      '-d', JSON.stringify({ contents: [{ parts: [{ text: pregunta }] }], tools: [{ google_search: {} }], generationConfig: { maxOutputTokens: 8000 } })], { maxBuffer: 50e6 }).toString());
    return Array.isArray(r) && r[0]?.error ? r[0] : r;
  });
  const cand = crudo.candidates?.[0];
  texto = (cand?.content?.parts || []).map(p => p.text || '').join('');
  const chunks = cand?.groundingMetadata?.groundingChunks || [];
  urls = chunks.map(c => c.web?.uri).filter(Boolean);
  // Las URLs de grounding son redirecciones de Google: el dominio real viaja en el título.
  var dominios = [...new Set(chunks.map(c => c.web?.title).filter(Boolean))];
  var consultas = cand?.groundingMetadata?.webSearchQueries || [];
} else { console.error('proveedor desconocido'); process.exit(1); }

urls = [...new Set(urls.map(u => u.replace(/[?&]utm_[^&]+/g, '')))];
const huellas = detectarHuellas(texto);
const res = { fecha: new Date().toISOString(), huellas, proveedor: prov, pregunta, cita_pescamapa: urls.some(u => /pescamapa\.com/.test(u)) || /pescamapa/i.test(texto) || (typeof dominios !== 'undefined' && dominios.some(d => /pescamapa\.com/.test(d))), urls, dominios: typeof dominios !== 'undefined' ? dominios : undefined, consultas: typeof consultas !== 'undefined' ? consultas : undefined, texto, error: crudo.error || (texto ? null : 'respuesta vacía') };
if (salida) { mkdirSync(dirname(salida), { recursive: true }); writeFileSync(salida, JSON.stringify(res, null, 2)); }
console.log(JSON.stringify({ ...res, texto: res.texto.slice(0, 400) }, null, 1));
