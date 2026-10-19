// Paths of draft pages, read straight from the content files so the sitemap
// filter (which runs outside Astro's content layer) agrees with the pages.
import { readFileSync, readdirSync } from 'node:fs';

function front(file) {
	const m = readFileSync(file, 'utf8').match(/^---\n([\s\S]*?)\n---/);
	const get = (k) => m?.[1].match(new RegExp(`^${k}:\\s*"?([^"\\n]+)"?`, 'm'))?.[1].trim();
	return { estado: get('estado'), region: get('region'), provincia: get('provincia'), slug: get('slug') };
}

export function borradores() {
	const out = [];
	for (const f of readdirSync('src/content/destinos')) {
		const d = front(`src/content/destinos/${f}`);
		if (d.estado !== 'publicado') out.push(`/${d.region}/${d.provincia}/${d.slug}/`);
	}
	for (const f of readdirSync('src/content/especies')) {
		if (front(`src/content/especies/${f}`).estado !== 'publicado') out.push(`/especies/${f.replace(/\.md$/, '')}/`);
	}
	return out;
}
