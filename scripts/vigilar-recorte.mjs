// Per-source trimming of the normalized page text, so the weekly watch only compares the
// part of a page that matters (news lists and counters on municipal pages looked like changes).
// Optional fields of a source in vigilancia/fuentes.json, applied in this order:
//   "desde":    keep the text from the first occurrence of this phrase on (case-insensitive)
//   "hasta":    cut the text at the first occurrence of this phrase after "desde"
//   "ignorar":  list of regular expressions (case-insensitive) removed from the text
//   "contiene": list of words/phrases; keep only the sentences that contain at least one
// A source without any of them is compared whole, as before. The function is idempotent:
// applying it to text it already trimmed gives the same text (the watch re-trims last
// week's stored copy, so adding a trim never shows up as a change in the page).

const SEP = /\s+¶\s+|(?<=[.!?])\s+|\s{2,}/;

export function recortar(texto, f) {
	let t = texto;
	if (f.desde) {
		const i = t.toLowerCase().indexOf(f.desde.toLowerCase());
		if (i >= 0) t = t.slice(i);
	}
	if (f.hasta) {
		const i = t.toLowerCase().indexOf(f.hasta.toLowerCase());
		if (i > 0) t = t.slice(0, i);
	}
	for (const r of f.ignorar ?? []) t = t.replace(new RegExp(r, 'gi'), ' ');
	if (f.contiene?.length) {
		const claves = f.contiene.map((c) => c.toLowerCase());
		t = t
			.split(SEP)
			.map((s) => s.trim())
			.filter((s) => s && claves.some((c) => s.toLowerCase().includes(c)))
			.join(' ¶ ');
	}
	return t.replace(/\s+/g, ' ').trim();
}
