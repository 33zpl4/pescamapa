import type { CollectionEntry } from 'astro:content';

export const REGIONES: Record<string, string> = {
	litoral: 'Litoral',
	patagonia: 'Patagonia',
	pampa: 'Pampa y lagunas',
	costa: 'Costa atlántica',
	'centro-norte': 'Centro y norte',
};

export const PROVINCIAS: Record<string, string> = {
	corrientes: 'Corrientes',
	'entre-rios': 'Entre Ríos',
	'santa-fe': 'Santa Fe',
	neuquen: 'Neuquén',
	'tierra-del-fuego': 'Tierra del Fuego',
	'buenos-aires': 'Buenos Aires',
};

export const urlDestino = (d: CollectionEntry<'destinos'>['data']) => `/${d.region}/${d.provincia}/${d.slug}/`;
export const urlEspecie = (id: string) => `/especies/${id}/`;

export const fecha = (d: Date) =>
	d.toLocaleDateString('es-AR', { day: 'numeric', month: 'long', year: 'numeric', timeZone: 'UTC' });
