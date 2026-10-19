// The data model. Every destination and species is a file; pages, maps,
// breadcrumbs and JSON-LD are generated from these fields, never typed twice.
//
// Trust rule (see CLAUDE.md): a page with `estado: publicado` must cite at
// least one official source and carry a regulation check younger than
// MAX_AGE_DAYS. The build fails otherwise. Drafts render with noindex and a
// visible banner, so they can be reviewed on the preview without Google
// ever seeing unverified rules.

import { defineCollection, reference } from 'astro:content';
import { glob } from 'astro/loaders';
import { z } from 'astro/zod';

const MAX_AGE_DAYS = 365;
const DAY = 86_400_000;

const fuente = z.object({
	nombre: z.string(),
	url: z.url(),
	oficial: z.boolean().default(false),
	consultada: z.coerce.date(),
});

const faq = z.object({ pregunta: z.string(), respuesta: z.string() });

// The web editor (Pages CMS, see .pages.yml) saves untouched optional fields
// as "" or null; treat both as absent so a blank field never breaks a build.
const vacio = (v: unknown) => (v === '' || v === null ? undefined : v);
const opcional = <T extends z.ZodType>(s: T) => z.preprocess(vacio, s.optional());
const lista = <T extends z.ZodType>(s: T) => z.preprocess(vacio, z.array(s).default([]));

// Coordinates are edited as two plain numbers; code reads `coordenadas`.
const lat = z.number().min(-56).max(-21);
const lon = z.number().min(-74).max(-53);

// Photo: a Commons key from src/data/fotos.json, or a file uploaded from the
// editor (src/assets/fotos/…) with its credit in `foto_credito`.
const foto = { foto: opcional(z.string()), foto_credito: opcional(z.string()) };

const estado = z.enum(['borrador', 'publicado']);

// Translations are separate files sharing a `clave`. Spanish is the source:
// a translation may not be published while the Spanish page isn't (checked
// in the page templates, which see both).
const idioma = z.enum(['es', 'pt', 'en']).default('es');
const clave = z.string().regex(/^[a-z0-9-]+$/);

// Pages that publish must prove their facts; drafts may not yet.
function confiable(d: { estado: string; fuentes: z.infer<typeof fuente>[]; reglamento?: { verificado?: Date } }, ctx: z.RefinementCtx) {
	if (d.estado !== 'publicado') return;
	if (!d.fuentes.some((f) => f.oficial)) {
		ctx.addIssue({ code: 'custom', message: 'publicado sin ninguna fuente oficial' });
	}
	if (d.reglamento) {
		const v = d.reglamento.verificado;
		if (!v) ctx.addIssue({ code: 'custom', message: 'publicado con el reglamento sin verificar (reglamento.verificado)' });
		else if (Date.now() - v.getTime() > MAX_AGE_DAYS * DAY) {
			ctx.addIssue({ code: 'custom', message: `reglamento verificado hace más de ${MAX_AGE_DAYS} días: revisar y actualizar la fecha` });
		}
	}
}

const destinos = defineCollection({
	loader: glob({ pattern: '**/*.md', base: './src/content/destinos' }),
	schema: z
		.object({
			estado,
			idioma,
			clave,
			nombre: z.string(),
			// Stable URL, no dates: /<region>/<provincia>/<slug>/
			region: z.enum(['litoral', 'patagonia', 'pampa', 'costa', 'centro-norte']),
			provincia: z.string(),
			slug: z.string().regex(/^[a-z0-9-]+$/),
			...foto,
			title: z.string().max(65),
			description: z.string().min(70).max(160),
			h1: z.string(),
			bajada: z.string(),
			lat,
			lon,
			// Spanish species ids (= their clave); templates resolve the translation.
			especies: z.array(reference('especies')).min(1),
			modalidades: z.array(z.string()).min(1),
			epoca: z.string(),
			aguas: lista(z.object({ nombre: z.string(), lat: opcional(lat), lon: opcional(lon), nota: z.string() })),
			temporada: lista(z.object({ meses: z.string(), que: z.string() })),
			reglamento: z.object({
				autoridad: z.string(),
				url: opcional(z.url()),
				verificado: opcional(z.coerce.date()),
				resumen: z.string(),
			}),
			faqs: lista(faq),
			fuentes: lista(fuente),
			actualizado: z.coerce.date(),
			autor: z.string(),
		})
		.superRefine(confiable)
		.transform((d) => ({
			...d,
			coordenadas: [d.lat, d.lon] as [number, number],
			aguas: d.aguas.map((a) => ({ ...a, coordenadas: a.lat != null && a.lon != null ? ([a.lat, a.lon] as [number, number]) : undefined })),
		})),
});

const especies = defineCollection({
	loader: glob({ pattern: '**/*.md', base: './src/content/especies' }),
	schema: z
		.object({
			estado,
			idioma,
			clave,
			nombre: z.string(),
			cientifico: z.string(),
			title: z.string().max(65),
			description: z.string().min(70).max(160),
			resumen: z.string(),
			fuentes: lista(fuente),
			actualizado: z.coerce.date(),
		})
		.superRefine(confiable),
});

// Destinations announced as "coming soon": one small file per place. When a
// full guide with the same `clave` exists, the planned card disappears.
const proximos = defineCollection({
	loader: glob({ pattern: '*.md', base: './src/content/proximos' }),
	schema: z
		.object({
			nombre: z.string(),
			nombre_pt: opcional(z.string()),
			nombre_en: opcional(z.string()),
			provincia: z.string(),
			region: z.enum(['litoral', 'patagonia', 'pampa', 'costa', 'centro-norte']),
			agua: z.string(),
			agua_pt: opcional(z.string()),
			agua_en: opcional(z.string()),
			especies: lista(z.string()),
			lat,
			lon,
			...foto,
			orden: z.preprocess(vacio, z.number().default(99)),
		})
		.transform((p) => ({
			...p,
			coordenadas: [p.lat, p.lon] as [number, number],
			nombres: { es: p.nombre, pt: p.nombre_pt ?? p.nombre, en: p.nombre_en ?? p.nombre },
			aguaPorIdioma: { es: p.agua, pt: p.agua_pt ?? p.agua, en: p.agua_en ?? p.agua },
		})),
});

export const collections = { destinos, especies, proximos };
