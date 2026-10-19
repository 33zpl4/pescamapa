// Photos with their credits. Two sources:
// - Wikimedia Commons (scripts/commons.py): the key names the file in
//   src/assets/fotos/ and its author/licence entry in src/data/fotos.json.
// - Uploaded from the web editor: the field holds the file path and the
//   entry's `foto_autor` names who took it (shown as "Foto: Ana Gómez").
// A photo with no credit from either source is a build error: CC BY / BY-SA
// require attribution, and our own photos deserve it too.
import type { ImageMetadata } from 'astro';
import creditos from '../data/fotos.json';

const archivos = import.meta.glob<{ default: ImageMetadata }>('../assets/fotos/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', { eager: true });
const porNombre = new Map(Object.entries(archivos).map(([ruta, mod]) => [ruta.split('/').pop()!.replace(/\.[^.]+$/, ''), mod.default]));

export interface Credito {
	archivo: string;
	pagina: string;
	autor: string;
	licencia: string;
	licencia_url: string;
	descripcion: string;
}

export interface Foto { src: ImageMetadata; credito: Credito }

const COMMONS = creditos as Record<string, Credito>;

export function foto(ref?: string, autor?: string): Foto | undefined {
	if (!ref) return undefined;
	const clave = ref.split('/').pop()!.replace(/\.[^.]+$/, '');
	const src = porNombre.get(clave);
	if (!src) throw new Error(`no encuentro la foto "${ref}" en src/assets/fotos/`);
	const credito = COMMONS[clave] ?? (autor
		? { archivo: clave, pagina: '', autor: autor.replace(/^foto:\s*/i, ''), licencia: '', licencia_url: '', descripcion: '' }
		: undefined);
	if (!credito) throw new Error(`la foto "${ref}" no tiene autor: completá "Autor de la foto" en el editor`);
	return { src, credito };
}

export const todosLosCreditos = () => Object.entries(COMMONS);
