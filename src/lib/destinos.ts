// Destinations with the language-independent fields (region, province,
// location, species, photo) taken from the Spanish file, so the photo or the
// pin is set once and every language follows. Use this instead of
// getCollection('destinos') everywhere.
import { getCollection, type CollectionEntry } from 'astro:content';

const COMPARTIDOS = ['region', 'provincia', 'lat', 'lon', 'especies', 'foto', 'foto_autor', 'foto_origen'] as const;

type Base = CollectionEntry<'destinos'>['data'];
export type DatosDestino = Omit<Base, 'region' | 'provincia' | 'lat' | 'lon'> & {
	region: NonNullable<Base['region']>;
	provincia: string;
	coordenadas: [number, number];
	aguas: (Base['aguas'][number] & { coordenadas?: [number, number] })[];
};
export type Destino = Omit<CollectionEntry<'destinos'>, 'data'> & { data: DatosDestino };

export async function getDestinos(): Promise<Destino[]> {
	const todos = await getCollection('destinos');
	return todos.map((e) => {
		const fuente = todos.find((x) => x.data.clave === e.data.clave && x.data.idioma === 'es');
		if (!fuente) throw new Error(`${e.id}: no hay versión en español con clave "${e.data.clave}"`);
		const comun = Object.fromEntries(COMPARTIDOS.map((k) => [k, fuente.data[k]]));
		const d = { ...e.data, ...comun } as Base;
		return {
			...e,
			data: {
				...d,
				region: d.region!,
				provincia: d.provincia!,
				coordenadas: [d.lat!, d.lon!],
				aguas: d.aguas.map((a) => ({ ...a, coordenadas: a.lat != null && a.lon != null ? ([a.lat, a.lon] as [number, number]) : undefined })),
			},
		};
	});
}
