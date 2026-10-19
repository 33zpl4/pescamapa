import { META, type Idioma } from '../i18n/ui';

export const REGIONES: Record<string, Record<Idioma, string>> = {
	litoral: { es: 'Litoral', pt: 'Litoral', en: 'Litoral' },
	patagonia: { es: 'Patagonia', pt: 'Patagônia', en: 'Patagonia' },
	pampa: { es: 'Pampa y lagunas', pt: 'Pampa e lagoas', en: 'Pampas and lagoons' },
	costa: { es: 'Costa atlántica', pt: 'Costa atlântica', en: 'Atlantic coast' },
	'centro-norte': { es: 'Centro y norte', pt: 'Centro e norte', en: 'Centre and north' },
};

export const PROVINCIAS: Record<string, string> = {
	corrientes: 'Corrientes',
	'entre-rios': 'Entre Ríos',
	'santa-fe': 'Santa Fe',
	neuquen: 'Neuquén',
	'tierra-del-fuego': 'Tierra del Fuego',
	'buenos-aires': 'Buenos Aires',
};

const SEG: Record<Idioma, { especies: string; mapa: string; creditos: string }> = {
	es: { especies: 'especies', mapa: 'mapa', creditos: 'creditos' },
	pt: { especies: 'especies', mapa: 'mapa', creditos: 'creditos' },
	en: { especies: 'species', mapa: 'map', creditos: 'credits' },
};

const p = (idioma: Idioma) => META[idioma].prefijo;

export const urlInicio = (idioma: Idioma) => `${p(idioma)}/`;
export const urlMapa = (idioma: Idioma) => `${p(idioma)}/${SEG[idioma].mapa}/`;
export const urlCreditos = (idioma: Idioma) => `${p(idioma)}/${SEG[idioma].creditos}/`;
export const urlEspecie = (clave: string, idioma: Idioma) => `${p(idioma)}/${SEG[idioma].especies}/${clave}/`;
export const urlDestino = (d: { idioma: Idioma; region: string; provincia: string; slug: string }) =>
	`${p(d.idioma)}/${d.region}/${d.provincia}/${d.slug}/`;

const LOCALE: Record<Idioma, string> = { es: 'es-AR', pt: 'pt-BR', en: 'en-GB' };
export const fecha = (d: Date, idioma: Idioma) =>
	d.toLocaleDateString(LOCALE[idioma], { day: 'numeric', month: 'long', year: 'numeric', timeZone: 'UTC' });
