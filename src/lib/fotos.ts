// Photos with their credits. Files come from Wikimedia Commons via
// scripts/commons.py (free licences only); fotos.json holds author, licence
// and source for each. A photo without a credit entry is a build error:
// CC BY / BY-SA require attribution wherever the photo is shown.
import type { ImageMetadata } from 'astro';
import creditos from '../data/fotos.json';

const archivos = import.meta.glob<{ default: ImageMetadata }>('../assets/fotos/*.jpg', { eager: true });

export interface Credito {
	archivo: string;
	pagina: string;
	autor: string;
	licencia: string;
	licencia_url: string;
	descripcion: string;
}

export interface Foto { src: ImageMetadata; credito: Credito }

export function foto(id?: string): Foto | undefined {
	if (!id) return undefined;
	const mod = archivos[`../assets/fotos/${id}.jpg`];
	if (!mod) throw new Error(`falta src/assets/fotos/${id}.jpg`);
	const credito = (creditos as Record<string, Credito>)[id];
	if (!credito) throw new Error(`la foto ${id} no tiene crédito en src/data/fotos.json`);
	return { src: mod.default, credito };
}

export const todosLosCreditos = () => Object.entries(creditos as Record<string, Credito>);
