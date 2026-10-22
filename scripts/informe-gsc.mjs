// Weekly Search Console report: traffic (last 7 days vs the 7 before) and indexing.
//   GSC_SERVICE_ACCOUNT='<json key>' GSC_SITE='sc-domain:pescamapa.com' node scripts/informe-gsc.mjs <salida.md>
// Read-only (webmasters.readonly). Without the secrets it writes a note and exits 0,
// so the weekly report still goes out. Search Console data lags ~2 days: the window
// ends 2 days ago. Setup steps for the owner: docs/vigilancia.md.

import { createSign } from 'node:crypto';
import { writeFileSync } from 'node:fs';

const [salida] = process.argv.slice(2);
const SITE = process.env.GSC_SITE;
const CLAVE = process.env.GSC_SERVICE_ACCOUNT;
const SITIO_WEB = 'https://pescamapa.com';

if (!SITE || !CLAVE) {
	const nota = '## Search Console\n\nTodavía no está conectado (faltan los secretos `GSC_SERVICE_ACCOUNT` y `GSC_SITE`). Pasos en `docs/vigilancia.md`.\n';
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
	const api = async (url, cuerpo) => {
		const r = await fetch(url, { method: 'POST', headers: { authorization: `Bearer ${t}`, 'content-type': 'application/json' }, body: JSON.stringify(cuerpo) });
		const j = await r.json();
		if (j.error) throw new Error(`${j.error.code} ${j.error.message}`);
		return j;
	};
	const consulta = (inicio, fin, dimensiones = [], limite = 10) =>
		api(`https://searchconsole.googleapis.com/webmasters/v3/sites/${encodeURIComponent(SITE)}/searchAnalytics/query`, { startDate: inicio, endDate: fin, dimensions: dimensiones, rowLimit: limite });

	const fin = dia(2), ini = dia(8), finPrev = dia(9), iniPrev = dia(15);
	const tot = async (a, b) => (await consulta(a, b)).rows?.[0] ?? { clicks: 0, impressions: 0, ctr: 0, position: 0 };
	const [ahora, antes] = [await tot(ini, fin), await tot(iniPrev, finPrev)];
	const flecha = (a, b) => (b === 0 ? (a ? '🆕' : '–') : a > b ? `▲ ${a - b}` : a < b ? `▼ ${b - a}` : '=');

	const L = ['## Search Console', '', `Semana ${ini} a ${fin} (contra ${iniPrev} a ${finPrev}).`, ''];
	L.push('| | Esta semana | Anterior | |', '| --- | ---: | ---: | --- |');
	L.push(`| Clics | ${ahora.clicks} | ${antes.clicks} | ${flecha(ahora.clicks, antes.clicks)} |`);
	L.push(`| Impresiones | ${ahora.impressions} | ${antes.impressions} | ${flecha(ahora.impressions, antes.impressions)} |`);
	L.push(`| Posición media | ${ahora.position ? ahora.position.toFixed(1) : '–'} | ${antes.position ? antes.position.toFixed(1) : '–'} | |`, '');

	const paginas = (await consulta(ini, fin, ['page'], 10)).rows ?? [];
	const consultas = (await consulta(ini, fin, ['query'], 10)).rows ?? [];
	L.push('**Páginas con más impresiones**', '');
	for (const r of paginas) L.push(`- ${r.keys[0].replace(SITIO_WEB, '')}: ${r.impressions} impr., ${r.clicks} clics`);
	if (!paginas.length) L.push('- (todavía sin datos)');
	L.push('', '**Búsquedas**', '');
	for (const r of consultas) L.push(`- “${r.keys[0]}”: ${r.impressions} impr., ${r.clicks} clics, posición ${r.position.toFixed(1)}`);
	if (!consultas.length) L.push('- (todavía sin datos)');

	// Indexing: inspect the URLs of our sitemap (capped).
	L.push('', '### Indexación', '');
	const loc = (xml) => [...xml.matchAll(/<loc>([^<]+)<\/loc>/g)].map((m) => m[1]);
	const indice = loc(await (await fetch(`${SITIO_WEB}/wp-sitemap.xml`)).text());
	let urls = [];
	for (const s of indice) urls.push(...loc(await (await fetch(s)).text()));
	urls = [...new Set(urls)].slice(0, 60);
	const por = {};
	const noIndexadas = [];
	for (const u of urls) {
		try {
			const j = await api('https://searchconsole.googleapis.com/v1/urlInspection/index:inspect', { inspectionUrl: u, siteUrl: SITE });
			const est = j.inspectionResult?.indexStatusResult?.coverageState ?? 'sin dato';
			por[est] = (por[est] ?? 0) + 1;
			if (!/^Submitted and indexed|^Indexed|Enviada e indexada|indexada/i.test(est)) noIndexadas.push(`${u.replace(SITIO_WEB, '')}: ${est}`);
		} catch (e) {
			por['error al consultar'] = (por['error al consultar'] ?? 0) + 1;
		}
		await new Promise((r) => setTimeout(r, 400));
	}
	L.push(`${urls.length} páginas del sitemap revisadas:`, '');
	for (const [k, v] of Object.entries(por)) L.push(`- ${k}: ${v}`);
	if (noIndexadas.length) {
		L.push('', '**Sin indexar (o con dato dudoso):**', '');
		for (const x of noIndexadas.slice(0, 20)) L.push(`- ${x}`);
	}
	writeFileSync(salida, L.join('\n') + '\n');
	console.log(L.join('\n'));
} catch (e) {
	const nota = `## Search Console\n\nNo pude leer los datos: ${e.message}. Revisá que la cuenta de servicio sea usuaria de la propiedad (docs/vigilancia.md).\n`;
	writeFileSync(salida, nota);
	console.log(nota);
}
