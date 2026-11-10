// Search Console, búsqueda × página (solo lectura, mismo acceso que informe-gsc.mjs).
//   GSC_SERVICE_ACCOUNT='<json key>' GSC_SITE='sc-domain:pescamapa.com' [DIAS=28] [TOP=60] node scripts/gsc-consultas.mjs <salida.md>
// Responde: para cada búsqueda con impresiones, qué página de Pescamapa muestra Google y en qué posición. Sin eso no se sabe si la
// página que elegimos para una búsqueda es la que Google muestra. Los datos de Search Console llegan con ~2 días de atraso.
import { createSign } from 'node:crypto';
import { writeFileSync } from 'node:fs';

const [salida] = process.argv.slice(2);
const SITE = process.env.GSC_SITE;
const CLAVE = process.env.GSC_SERVICE_ACCOUNT;
const DIAS = Number(process.env.DIAS || 28);
const TOP = Number(process.env.TOP || 60);
const WEB = 'https://pescamapa.com';
if (!SITE || !CLAVE) {
	const nota = '## Búsqueda × página\n\nFaltan los secretos `GSC_SERVICE_ACCOUNT` y `GSC_SITE`.\n';
	writeFileSync(salida, nota);
	console.log(nota);
	process.exit(0);
}
const b64 = (x) => Buffer.from(typeof x === 'string' ? x : JSON.stringify(x)).toString('base64url');
async function token() {
	const k = JSON.parse(CLAVE);
	const ahora = Math.floor(Date.now() / 1000);
	const cuerpo = `${b64({ alg: 'RS256', typ: 'JWT' })}.${b64({ iss: k.client_email, scope: 'https://www.googleapis.com/auth/webmasters.readonly', aud: 'https://oauth2.googleapis.com/token', iat: ahora, exp: ahora + 3000 })}`;
	const firma = createSign('RSA-SHA256').update(cuerpo).sign(k.private_key, 'base64url');
	const r = await fetch('https://oauth2.googleapis.com/token', { method: 'POST', headers: { 'content-type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams({ grant_type: 'urn:ietf:params:oauth:grant-type:jwt-bearer', assertion: `${cuerpo}.${firma}` }) });
	const j = await r.json();
	if (!j.access_token) throw new Error('No se pudo obtener el token: ' + JSON.stringify(j).slice(0, 200));
	return j.access_token;
}
const dia = (n) => new Date(Date.now() - n * 864e5).toISOString().slice(0, 10);
try {
	const t = await token();
	const r = await fetch(`https://searchconsole.googleapis.com/webmasters/v3/sites/${encodeURIComponent(SITE)}/searchAnalytics/query`, {
		method: 'POST', headers: { authorization: `Bearer ${t}`, 'content-type': 'application/json' },
		body: JSON.stringify({ startDate: dia(DIAS + 2), endDate: dia(2), dimensions: ['query', 'page'], rowLimit: 5000, type: 'web' }),
	});
	const j = await r.json();
	if (j.error) throw new Error(`${j.error.code} ${j.error.message}`);
	const filas = j.rows ?? [];
	const por = new Map();
	for (const f of filas) {
		const [q, p] = f.keys;
		const e = por.get(q) ?? { q, impr: 0, clics: 0, pos: 0, paginas: [] };
		e.impr += f.impressions; e.clics += f.clicks; e.pos += f.position * f.impressions;
		e.paginas.push({ p: p.replace(WEB, '') || '/', impr: f.impressions, clics: f.clicks, pos: f.position });
		por.set(q, e);
	}
	const lista = [...por.values()].map((e) => ({ ...e, pos: e.pos / e.impr })).sort((a, b) => b.impr - a.impr);
	const L = [`## Búsqueda × página, ${dia(DIAS + 2)} a ${dia(2)} (web)`, '', `${lista.length} búsquedas distintas, ${filas.length} pares búsqueda-página, ${lista.reduce((s, e) => s + e.impr, 0)} impresiones, ${lista.reduce((s, e) => s + e.clics, 0)} clics.`, ''];
	L.push('| Búsqueda | Impr. | Clics | Pos. media | Páginas que Google muestra (impr., pos.) |', '| --- | ---: | ---: | ---: | --- |');
	for (const e of lista.slice(0, TOP)) {
		const pg = e.paginas.sort((a, b) => b.impr - a.impr).slice(0, 3).map((x) => `${x.p} (${x.impr}, ${x.pos.toFixed(0)})`).join('<br>');
		L.push(`| ${e.q} | ${e.impr} | ${e.clics} | ${e.pos.toFixed(0)} | ${pg} |`);
	}
	writeFileSync(salida, L.join('\n') + '\n');
	console.log(L.join('\n'));
} catch (e) {
	const nota = `## Búsqueda × página\n\nNo pude leer los datos: ${e.message}.\n`;
	writeFileSync(salida, nota);
	console.log(nota);
}
