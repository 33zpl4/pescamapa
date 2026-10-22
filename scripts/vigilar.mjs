// Weekly watch of the sources our guides depend on (vigilancia/fuentes.json).
//   node scripts/vigilar.mjs <fuentes.json> <estado.json> <salida.md>
// No model, no tokens: it fetches each page, normalizes the text, and compares it
// with last week's copy kept in <estado.json>. It reports what stopped responding
// and, for pages that changed, the few sentences added or removed. The first
// time a source is seen it is only recorded (baseline), never flagged.
//
// "texto": compares the page text; "pdf": compares the file; "estado": only checks
// that it still responds (outfitter sites and live dashboards change all the time).

import { createHash } from 'node:crypto';
import { readFileSync, writeFileSync, existsSync } from 'node:fs';

const [rutaFuentes, rutaEstado, rutaSalida] = process.argv.slice(2);
const fuentes = JSON.parse(readFileSync(rutaFuentes, 'utf8'));
const estado = existsSync(rutaEstado) ? JSON.parse(readFileSync(rutaEstado, 'utf8')) : {};
const hoy = new Date().toISOString().slice(0, 10);
const MAX_GUARDADO = 60_000; // characters of normalized text kept per source, to show the diff next week

const sha = (x) => createHash('sha256').update(x).digest('hex');
const dormir = (ms) => new Promise((r) => setTimeout(r, ms));

/** Numeric character references (&#116; / &#x74;): some sites (Cloudflare e-mail protection)
 *  re-encode e-mail addresses differently on every load, which looked like a change. */
export function decodificar(t) {
	return t
		.replace(/&#(\d+);/g, (_, n) => String.fromCodePoint(Number(n)))
		.replace(/&#x([0-9a-f]+);/gi, (_, h) => String.fromCodePoint(parseInt(h, 16)));
}

/** Page text without markup or scripts, whitespace collapsed. */
export function normalizar(html) {
	return html
		.replace(/<!--[\s\S]*?-->/g, ' ')
		.replace(/<(script|style|noscript|svg|iframe)\b[\s\S]*?<\/\1>/gi, ' ')
		.replace(/<[^>]+>/g, ' ')
		.replace(/&nbsp;/g, ' ').replace(/&amp;/g, '&').replace(/&quot;/g, '"').replace(/&#39;/g, "'").replace(/&lt;/g, '<').replace(/&gt;/g, '>')
		.replace(/&#(\d+);|&#x[0-9a-f]+;/gi, (m) => decodificar(m))
		.replace(/\b\d{1,2}:\d{2}(:\d{2})?\b/g, ' ') // clocks change every load
		.replace(/\s+/g, ' ')
		.trim();
}

/** Sentences present in only one of the two texts. */
export function diferencias(antes, despues) {
	const frases = (t) => new Set(t.split(/(?<=[.!?])\s+|\s{2,}/).map((f) => f.trim()).filter((f) => f.length > 25));
	const a = frases(antes);
	const d = frases(despues);
	return {
		nuevas: [...d].filter((f) => !a.has(f)).slice(0, 5),
		quitadas: [...a].filter((f) => !d.has(f)).slice(0, 5),
	};
}

// Some official servers send their certificate without the intermediate one, so Node
// answers UNABLE_TO_VERIFY_LEAF_SIGNATURE even though browsers load the site. We do what
// browsers do: read the issuer's address from the server's own certificate ("CA Issuers"),
// download that intermediate, and retry with VERIFICATION ON against the system roots plus
// that intermediate. The first connection only reads the public certificate and sends no
// request; the page is never fetched over an unverified connection.
import tls from 'node:tls';
import https from 'node:https';
import http from 'node:http';
import { X509Certificate } from 'node:crypto';

const intermedios = new Map(); // host -> PEM[] found for it

function bajar(url) {
	return new Promise((resolve, reject) => {
		(url.startsWith('https') ? https : http)
			.get(url, { timeout: 15_000 }, (res) => {
				const partes = [];
				res.on('data', (c) => partes.push(c));
				res.on('end', () => resolve(Buffer.concat(partes)));
			})
			.on('timeout', function () { this.destroy(new Error('TIMEOUT')); })
			.on('error', reject);
	});
}

const aPem = (der) => `-----BEGIN CERTIFICATE-----\n${der.toString('base64').replace(/(.{64})/g, '$1\n')}\n-----END CERTIFICATE-----\n`;
const emisor = (cert) => /CA Issuers - URI:(\S+)/.exec(cert.infoAccess ?? '')?.[1];

async function completarCadena(host, puerto = 443) {
	const clave = `${host}:${puerto}`;
	if (intermedios.has(clave)) return intermedios.get(clave);
	const pem = [];
	try {
		const hoja = await new Promise((resolve, reject) => {
			const s = tls.connect({ host, port: puerto, servername: host, rejectUnauthorized: false, timeout: 15_000 }, () => {
				const c = s.getPeerCertificate(true);
				s.destroy();
				resolve(c.raw ? new X509Certificate(c.raw) : null);
			});
			s.on('error', reject);
			s.on('timeout', () => s.destroy(new Error('TIMEOUT')));
		});
		let actual = hoja;
		for (let i = 0; i < 3 && actual; i++) {
			const url = emisor(actual);
			if (!url) break;
			const der = await bajar(url);
			const sig = new X509Certificate(der);
			pem.push(aPem(der));
			if (sig.subject === sig.issuer) break; // reached a root
			actual = sig;
		}
	} catch {
		// leave what we found; the retry below will fail the same way and be reported
	}
	intermedios.set(clave, pem);
	return pem;
}

function traerEstricto(url, extra) {
	return new Promise((resolve) => {
		const extraEnv = process.env.NODE_EXTRA_CA_CERTS ? [readFileSync(process.env.NODE_EXTRA_CA_CERTS, 'utf8')] : [];
		const q = https.get(url, { ca: [...tls.rootCertificates, ...extraEnv, ...extra], timeout: 25_000, headers: { 'user-agent': 'Mozilla/5.0 (compatible; PescamapaWatch/1.0; +https://pescamapa.com)', accept: 'text/html,application/pdf,*/*' } }, (res) => {
			if (res.statusCode >= 300 && res.statusCode < 400 && res.headers.location) {
				res.resume();
				return resolve(traerEstricto(new URL(res.headers.location, url).href, extra));
			}
			const partes = [];
			res.on('data', (c) => partes.push(c));
			res.on('end', () => resolve({ status: res.statusCode, buf: Buffer.concat(partes) }));
		});
		q.on('timeout', () => q.destroy(new Error('TIMEOUT')));
		q.on('error', (e) => resolve({ status: 0, error: String(e.code || e.message) }));
	});
}

async function traer(url) {
	const r = await traerBasico(url);
	if (r.error !== 'UNABLE_TO_VERIFY_LEAF_SIGNATURE' || !url.startsWith('https')) return r;
	const u = new URL(url);
	const extra = await completarCadena(u.hostname, Number(u.port) || 443);
	return extra.length ? traerEstricto(url, extra) : r;
}

async function traerBasico(url) {
	const ctl = new AbortController();
	const t = setTimeout(() => ctl.abort(), 25_000);
	try {
		const r = await fetch(url, { signal: ctl.signal, redirect: 'follow', headers: { 'user-agent': 'Mozilla/5.0 (compatible; PescamapaWatch/1.0; +https://pescamapa.com)', accept: 'text/html,application/pdf,*/*' } });
		const buf = Buffer.from(await r.arrayBuffer());
		return { status: r.status, buf };
	} catch (e) {
		return { status: 0, error: String(e.cause?.code || e.name || e.message) };
	} finally {
		clearTimeout(t);
	}
}

const caidas = [];
const cambios = [];
const nuevas = [];
const sin_cambios = [];

for (const f of fuentes) {
	const r = await traer(f.url);
	const previo = estado[f.id];
	if (r.status === 0 || r.status >= 400) {
		const veces = (previo?.fallos ?? 0) + 1;
		estado[f.id] = { ...(previo ?? {}), fallos: veces, ultimoEstado: r.status || r.error, visto: hoy };
		caidas.push({ f, detalle: r.status || r.error, veces });
		await dormir(500);
		continue;
	}
	const registro = { ...(previo ?? {}), fallos: 0, ultimoEstado: r.status, visto: hoy };
	if (f.vigilar === 'estado') {
		estado[f.id] = registro;
		sin_cambios.push(f);
	} else {
		const texto = f.vigilar === 'pdf' ? '' : normalizar(r.buf.toString('utf8'));
		const huella = f.vigilar === 'pdf' ? sha(r.buf) : sha(texto);
		if (!previo?.huella) {
			estado[f.id] = { ...registro, huella, texto: texto.slice(0, MAX_GUARDADO), primeraVez: hoy };
			nuevas.push(f);
		} else if ((f.vigilar === 'pdf' || !previo.texto ? previo.huella : sha(normalizar(previo.texto))) !== huella) {
			// (text sources: the stored copy is re-normalized with today's rules, so a
			// change in the normalizer itself never shows up as a change in the page)
			const d = f.vigilar === 'pdf' ? { nuevas: [], quitadas: [] } : diferencias(previo.texto ?? '', texto);
			cambios.push({ f, d, antes: previo.visto });
			estado[f.id] = { ...registro, huella, texto: texto.slice(0, MAX_GUARDADO), cambiado: hoy };
		} else {
			estado[f.id] = { ...registro, huella, texto: previo.texto };
			sin_cambios.push(f);
		}
	}
	await dormir(500);
}

const corto = (s) => (s.length > 220 ? s.slice(0, 217) + '…' : s);
const L = [];
L.push(`## Vigilancia de fuentes — ${hoy}`, '');
L.push(`${fuentes.length} fuentes revisadas: ${caidas.length} sin respuesta, ${cambios.length} con cambios, ${nuevas.length} registradas por primera vez, ${sin_cambios.length} sin cambios.`, '');
if (caidas.length) {
	L.push('### ❌ No responden', '');
	for (const c of caidas) L.push(`- **${c.f.nombre}** (${c.f.url}): ${c.detalle}${c.veces > 1 ? `, ${c.veces} semanas seguidas` : ''}. Afecta: ${c.f.afecta}. ${c.f.accion}`);
	L.push('');
}
if (cambios.length) {
	L.push('### ⚠️ Cambió el contenido', '');
	for (const c of cambios) {
		L.push(`- **${c.f.nombre}** (${c.f.url}). Afecta: ${c.f.afecta}. **Qué hacer:** ${c.f.accion}`);
		if (c.f.vigilar === 'pdf') L.push('  - El archivo es distinto al de la semana pasada.');
		for (const s of c.d.nuevas) L.push(`  - Nuevo: «${corto(s)}»`);
		for (const s of c.d.quitadas) L.push(`  - Ya no está: «${corto(s)}»`);
		if (c.f.vigilar === 'texto' && !c.d.nuevas.length && !c.d.quitadas.length) L.push('  - Cambió el texto, pero no en frases completas (puede ser ruido de la página).');
	}
	L.push('');
}
if (nuevas.length) {
	L.push('### 🆕 Registradas hoy (desde la próxima semana se comparan)', '');
	for (const f of nuevas) L.push(`- ${f.nombre}`);
	L.push('');
}
if (!caidas.length && !cambios.length) L.push('Nada para hacer esta semana en las fuentes.', '');
writeFileSync(rutaSalida, L.join('\n'));
writeFileSync(rutaEstado, JSON.stringify(estado));
console.log(L.join('\n'));
