// Links between language versions of the same page (shared `clave`).
import { getCollection, type CollectionEntry } from 'astro:content';
import { IDIOMAS, type Idioma } from '../i18n/ui';
import { urlDestino, urlEspecie, urlInicio, urlMapa, urlCreditos } from './rutas';

export type Alternas = Partial<Record<Idioma, string>>;

// Pages that exist in every language.
export const alternasFijas = (url: (i: Idioma) => string): Alternas =>
	Object.fromEntries(IDIOMAS.map((i) => [i, url(i)]));
export const alternasInicio = () => alternasFijas(urlInicio);
export const alternasMapa = () => alternasFijas(urlMapa);
export const alternasCreditos = () => alternasFijas(urlCreditos);

export async function alternasDestino(d: CollectionEntry<'destinos'>['data']): Promise<Alternas> {
	const hermanos = (await getCollection('destinos')).filter((e) => e.data.clave === d.clave);
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
