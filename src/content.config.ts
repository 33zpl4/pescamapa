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
			// Key in src/data/fotos.json / src/assets/fotos/<foto>.jpg (free licence + credit).
			foto: z.string().optional(),
			title: z.string().max(65),
			description: z.string().min(70).max(160),
			h1: z.string(),
			bajada: z.string(),
			coordenadas: z.tuple([z.number(), z.number()]),
			// Spanish species ids (= their clave); templates resolve the translation.
			especies: z.array(reference('especies')).min(1),
			modalidades: z.array(z.string()).min(1),
			epoca: z.string(),
			aguas: z.array(z.object({ nombre: z.string(), coordenadas: z.tuple([z.number(), z.number()]).optional(), nota: z.string() })).default([]),
			temporada: z.array(z.object({ meses: z.string(), que: z.string() })).default([]),
			reglamento: z.object({
				autoridad: z.string(),
				url: z.url().optional(),
				verificado: z.coerce.date().optional(),
				resumen: z.string(),
			}),
			faqs: z.array(faq).default([]),
			fuentes: z.array(fuente).default([]),
			actualizado: z.coerce.date(),
			autor: z.string(),
		})
		.superRefine(confiable),
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
			fuentes: z.array(fuente).default([]),
			actualizado: z.coerce.date(),
		})
		.superRefine(confiable),
});

export const collections = { destinos, especies };
