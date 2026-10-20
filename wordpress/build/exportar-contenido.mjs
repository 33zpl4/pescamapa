// Exports the Astro content (src/content) as one JSON for the WordPress
// importer (wordpress/build/importar.php). Markdown bodies become block-editor
// markup so they stay editable block by block in wp-admin.
//
//   node wordpress/build/exportar-contenido.mjs > contenido.json
import { readFileSync, readdirSync } from 'node:fs';
import { join, basename } from 'node:path';
import { parse as yaml } from 'yaml';
import { marked } from 'marked';

const raiz = new URL('../../', import.meta.url).pathname;
const leer = (dir) =>
	readdirSync(join(raiz, dir), { recursive: true })
		.filter((f) => f.endsWith('.md'))
		.map((f) => {
			const texto = readFileSync(join(raiz, dir, f), 'utf8');
			const m = texto.match(/^---\n([\s\S]*?)\n---\n?([\s\S]*)$/);
			return { archivo: f, datos: yaml(m[1]), cuerpo: m[2].trim() };
		});

const fecha = (d) => (d ? String(d instanceof Date ? d.toISOString().slice(0, 10) : d).replaceAll('-', '') : ''); // ACF date_picker stores Ymd
const fotoArchivo = (ruta) => (ruta ? basename(ruta) : '');

// Markdown → block markup (paragraph, heading, list, quote).
function bloques(md) {
	if (!md) return '';
	return marked
		.lexer(md)
		.filter((t) => t.type !== 'space')
		.map((t) => {
			const html = marked.parser([t]).trim();
			if (t.type === 'heading') return `<!-- wp:heading {"level":${t.depth}} -->\n${html.replace(/<h(\d)/, '<h$1 class="wp-block-heading"')}\n<!-- /wp:heading -->`;
			if (t.type === 'paragraph') return `<!-- wp:paragraph -->\n${html}\n<!-- /wp:paragraph -->`;
			if (t.type === 'blockquote') {
				const inner = t.tokens.filter((x) => x.type !== 'space').map((x) => `<!-- wp:paragraph -->\n${marked.parser([x]).trim()}\n<!-- /wp:paragraph -->`).join('\n');
				return `<!-- wp:quote -->\n<blockquote class="wp-block-quote">${inner}</blockquote>\n<!-- /wp:quote -->`;
			}
			if (t.type === 'list') {
				const tag = t.ordered ? 'ol' : 'ul';
				const items = t.items.map((it) => `<!-- wp:list-item -->\n<li>${marked.parseInline(it.text)}</li>\n<!-- /wp:list-item -->`).join('\n');
				return `<!-- wp:list${t.ordered ? ' {"ordered":true}' : ''} -->\n<${tag} class="wp-block-list">${items}</${tag}>\n<!-- /wp:list -->`;
			}
			return `<!-- wp:html -->\n${html}\n<!-- /wp:html -->`;
		})
		.join('\n\n');
}

const fuentes = (f = []) => (f ?? []).map((x) => ({ nombre: x.nombre, url: x.url, oficial: x.oficial ? 1 : 0, consultada: fecha(x.consultada) }));

const especies = leer('src/content/especies').map(({ datos: d, cuerpo }) => ({
	tipo: 'pm_especie',
	idioma: d.idioma,
	clave: d.clave,
	titulo: d.nombre,
	slug: d.idioma === 'es' ? d.clave : `${d.clave}-${d.idioma}`,
	contenido: bloques(cuerpo),
	campos: {
		idioma: d.idioma, estado: d.estado, clave: d.clave,
		cientifico: d.cientifico, resumen: d.resumen,
		seo_title: d.title, seo_description: d.description,
		actualizado: fecha(d.actualizado), fuentes: fuentes(d.fuentes),
	},
}));

const destinos = leer('src/content/destinos').map(({ datos: d, cuerpo }) => ({
	tipo: 'pm_destino',
	idioma: d.idioma,
	clave: d.clave,
	titulo: d.nombre,
	slug: d.slug,
	foto: fotoArchivo(d.foto),
	contenido: bloques(cuerpo),
	campos: {
		idioma: d.idioma, estado: d.estado, clave: d.clave,
		...(d.idioma === 'es' ? { region: d.region, provincia: d.provincia, lat: d.lat, lon: d.lon, especies: d.especies } : {}),
		h1: d.h1, bajada: d.bajada,
		modalidades: (d.modalidades ?? []).join('\n'),
		epoca: d.epoca,
		aguas: (d.aguas ?? []).map((a) => ({ nombre: a.nombre, nota: a.nota, lat: a.lat ?? '', lon: a.lon ?? '' })),
		temporada: d.temporada ?? [],
		reglamento_autoridad: d.reglamento?.autoridad ?? '',
		reglamento_url: d.reglamento?.url ?? '',
		reglamento_verificado: fecha(d.reglamento?.verificado),
		reglamento_resumen: d.reglamento?.resumen ?? '',
		faqs: d.faqs ?? [],
		fuentes: fuentes(d.fuentes),
		seo_title: d.title, seo_description: d.description,
		actualizado: fecha(d.actualizado), autor: d.autor,
	},
}));

const proximos = leer('src/content/proximos').map(({ archivo, datos: d }) => ({
	tipo: 'pm_proximo',
	clave: archivo.replace(/\.md$/, ''),
	titulo: d.nombre,
	slug: archivo.replace(/\.md$/, ''),
	foto: fotoArchivo(d.foto),
	campos: {
		nombre_pt: d.nombre_pt ?? '', nombre_en: d.nombre_en ?? '',
		provincia: ({ 'Corrientes': 'corrientes', 'Entre Ríos': 'entre-rios', 'Santa Fe': 'santa-fe', 'Neuquén': 'neuquen', 'Tierra del Fuego': 'tierra-del-fuego', 'Buenos Aires': 'buenos-aires' })[d.provincia] ?? d.provincia,
		region: d.region, agua: d.agua, agua_pt: d.agua_pt ?? '', agua_en: d.agua_en ?? '',
		especies: d.especies ?? [], lat: d.lat, lon: d.lon, orden: d.orden ?? 99,
	},
}));

const creditos = JSON.parse(readFileSync(join(raiz, 'src/data/fotos.json'), 'utf8'));
const fotos = Object.fromEntries(
	Object.entries(creditos).map(([k, c]) => [`${k}.jpg`, {
		autor: c.autor, licencia: c.licencia, licencia_url: c.licencia_url, pagina: c.pagina,
		titulo: c.archivo.replace(/^File:/, '').replace(/\.[^.]+$/, ''), origen: 'licencia',
	}]),
);

process.stdout.write(JSON.stringify({ especies, destinos, proximos, fotos }, null, 1));
