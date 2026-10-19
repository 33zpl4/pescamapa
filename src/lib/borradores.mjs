// Paths of draft pages, read straight from the content files so the sitemap
// filter (which runs outside Astro's content layer) agrees with the pages.
// Mirrors src/lib/rutas.ts — keep the two in step.
import { readFileSync, readdirSync } from 'node:fs';
import { join } from 'node:path';

const PREFIJO = { es: '', pt: '/pt', en: '/en' };
const ESPECIES = { es: 'especies', pt: 'especies', en: 'species' };

function front(file) {
	const m = readFileSync(file, 'utf8').match(/^---\n([\s\S]*?)\n---/);
	const get = (k) => m?.[1].match(new RegExp(`^${k}:\\s*"?([^"\\n]+)"?`, 'm'))?.[1].trim();
	return { estado: get('estado'), idioma: get('idioma') ?? 'es', clave: get('clave'), region: get('region'), provincia: get('provincia'), slug: get('slug') };
}

const archivos = (dir) => readdirSync(dir, { recursive: true }).filter((f) => f.endsWith('.md')).map((f) => join(dir, f));

export function borradores() {
	const out = [];
	for (const f of archivos('src/content/destinos')) {
		const d = front(f);
		if (d.estado !== 'publicado') out.push(`${PREFIJO[d.idioma]}/${d.region}/${d.provincia}/${d.slug}/`);
	}
	for (const f of archivos('src/content/especies')) {
		const e = front(f);
		if (e.estado !== 'publicado') out.push(`${PREFIJO[e.idioma]}/${ESPECIES[e.idioma]}/${e.clave}/`);
	}
	return out;
}
