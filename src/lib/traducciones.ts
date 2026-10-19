// Links between language versions of the same page (shared `clave`).
import { getCollection } from 'astro:content';
import { getDestinos, type DatosDestino } from './destinos';
import { IDIOMAS, type Idioma } from '../i18n/ui';
import { urlDestino, urlEspecie, urlInicio, urlMapa, urlCreditos } from './rutas';

export type Alternas = Partial<Record<Idioma, string>>;

// Pages that exist in every language.
export const alternasFijas = (url: (i: Idioma) => string): Alternas =>
	Object.fromEntries(IDIOMAS.map((i) => [i, url(i)]));
export const alternasInicio = () => alternasFijas(urlInicio);
export const alternasMapa = () => alternasFijas(urlMapa);
export const alternasCreditos = () => alternasFijas(urlCreditos);

export async function alternasDestino(d: DatosDestino): Promise<Alternas> {
	const hermanos = (await getDestinos()).filter((e) => e.data.clave === d.clave);
	return Object.fromEntries(hermanos.map((e) => [e.data.idioma, urlDestino(e.data)]));
}

export async function alternasEspecie(clave: string): Promise<Alternas> {
	const hermanos = (await getCollection('especies')).filter((e) => e.data.clave === clave);
	return Object.fromEntries(hermanos.map((e) => [e.data.idioma, urlEspecie(clave, e.data.idioma)]));
}

// The species entry in the requested language, or the Spanish source.
export async function especieEn(clave: string, idioma: Idioma) {
	const todas = await getCollection('especies');
	return todas.find((e) => e.data.clave === clave && e.data.idioma === idioma) ?? todas.find((e) => e.data.clave === clave && e.data.idioma === 'es')!;
}

// A translation can't be more "published" than its Spanish source.
export async function fuentePublicada(coleccion: 'destinos' | 'especies', clave: string, idioma: Idioma, estado: string) {
	if (idioma === 'es' || estado !== 'publicado') return;
	const fuente = (await getCollection(coleccion)).find((e) => e.data.clave === clave && e.data.idioma === 'es');
	if (fuente?.data.estado !== 'publicado') {
		throw new Error(`${coleccion}/${clave} (${idioma}) está publicado pero la versión en español no`);
	}
}

// "Coming soon" places in this language, ordered, minus any that already
// have a full guide (same file name as the guide's `clave`).
export async function proximosVisibles(idioma: Idioma) {
	const guias = new Set((await getDestinos()).map((d) => d.data.clave));
	return (await getCollection('proximos'))
		.filter((p) => !guias.has(p.id))
		.sort((a, b) => a.data.orden - b.data.orden)
		.map((p) => ({
			id: p.id,
			nombre: p.data.nombres[idioma],
			agua: p.data.aguaPorIdioma[idioma],
			provincia: p.data.provincia,
			region: p.data.region,
			especies: p.data.especies,
			coordenadas: p.data.coordenadas,
			foto: p.data.foto,
			foto_autor: p.data.foto_autor,
		}));
}
