// "PENDIENTE:" marks a fact still to verify. Drafts show them; a published
// page containing one fails the build (the schema can't see the body).
export function sinPendientes(id: string, estado: string, ...textos: unknown[]) {
	if (estado !== 'publicado') return;
	const hit = textos.map((t) => JSON.stringify(t ?? '')).find((t) => t.includes('PENDIENTE'));
	if (hit) throw new Error(`${id}: está "publicado" pero todavía tiene marcas PENDIENTE`);
}
