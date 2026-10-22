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

/** Page text without markup or scripts, whitespace collapsed. */
export function normalizar(html) {
	return html
		.replace(/<!--[\s\S]*?-->/g, ' ')
		.replace(/<(script|style|noscript|svg|iframe)\b[\s\S]*?<\/\1>/gi, ' ')
		.replace(/<[^>]+>/g, ' ')
		.replace(/&nbsp;/g, ' ').replace(/&amp;/g, '&').replace(/&quot;/g, '"').replace(/&#39;/g, "'").replace(/&lt;/g, '<').replace(/&gt;/g, '>')
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

async function traer(url) {
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
		} else if (previo.huella !== huella) {
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
