// Brand check for the hand-written theme CSS and templates (docs/marca.md).
//   node wordpress/build/revisar-marca.mjs
// Fails (exit 1) on:
//   - a hex colour anywhere in extra.css or the theme PHP, outside the :root token blocks;
//   - a font-family that is not one of the three tokens;
//   - more literal pixel font sizes than BASELINE_TAMANOS (new CSS uses var(--fs-*));
//   - a colour/font token used but not defined in pescamapa.css / extra.css.
// pescamapa.css is generated from the Astro components and is not scanned.
// The baseline only shrinks: when you replace a literal size with a token,
// lower the number below in the same commit.

import { readFileSync, readdirSync } from 'node:fs';
import { join } from 'node:path';

const TEMA = new URL('../themes/pescamapa/', import.meta.url).pathname;
const BASELINE_TAMANOS = 0;

const quitarRoot = (css) => css.replace(/:root\s*\{[^}]*\}/g, '');
const quitarComentarios = (css) => css.replace(/\/\*[\s\S]*?\*\//g, '');

const archivos = (dir, ext) => readdirSync(dir, { withFileTypes: true }).flatMap((e) =>
	e.isDirectory() ? (['assets', 'build'].includes(e.name) ? [] : archivos(join(dir, e.name), ext)) : e.name.endsWith(ext) ? [join(dir, e.name)] : []);

const errores = [];
const extra = quitarComentarios(readFileSync(join(TEMA, 'assets/extra.css'), 'utf8'));
const extraSinRoot = quitarRoot(extra);

for (const m of extraSinRoot.matchAll(/#[0-9a-fA-F]{3,8}\b/g)) errores.push(`extra.css: color literal ${m[0]} (usá var(--…))`);
for (const m of extraSinRoot.matchAll(/font-family:\s*([^;}]+)/g)) {
	if (!/^var\(--font-(head|body|label)\)$/.test(m[1].trim())) errores.push(`extra.css: font-family "${m[1].trim()}" (usá var(--font-head|body|label))`);
}
for (const m of extraSinRoot.matchAll(/font:\s*([^;}]+)/g)) {
	if (!/var\(--font-(head|body|label)\)/.test(m[1])) errores.push(`extra.css: font sin token de familia: "${m[1].trim()}"`);
}
const tamanos = [...extraSinRoot.matchAll(/font-size:\s*[\d.]+px/g)].length + [...extraSinRoot.matchAll(/font:[^;}]*?\b\d+px\b/g)].length;
if (tamanos > BASELINE_TAMANOS) errores.push(`extra.css: ${tamanos} tamaños de fuente literales (baseline ${BASELINE_TAMANOS}); usá var(--fs-*)`);
else if (tamanos < BASELINE_TAMANOS) console.log(`· Bajá BASELINE_TAMANOS a ${tamanos} en este archivo.`);

for (const f of archivos(TEMA, '.php')) {
	const php = readFileSync(f, 'utf8');
	for (const m of php.matchAll(/style="[^"]*"/g)) {
		if (/#[0-9a-fA-F]{3,8}\b|font-family|font-size/.test(m[0])) errores.push(`${f.replace(TEMA, '')}: estilo inline con color/tipografía (${m[0].slice(0, 60)})`);
	}
}

const tokens = new Set([...readFileSync(join(TEMA, 'assets/pescamapa.css'), 'utf8').matchAll(/(--[a-z0-9-]+)\s*:/g)].map((m) => m[1]));
for (const m of extra.matchAll(/(--[a-z0-9-]+)\s*:/g)) tokens.add(m[1]);
for (const m of extra.matchAll(/var\((--[a-z0-9-]+)/g)) if (!tokens.has(m[1])) errores.push(`extra.css: usa ${m[1]}, que no está definido`);

if (errores.length) {
	console.error(['✖ Reglas de marca (docs/marca.md):', ...[...new Set(errores)].map((e) => '  - ' + e)].join('\n'));
	process.exit(1);
}
console.log(`✓ Marca: sin colores ni familias fuera de los tokens; ${tamanos} tamaños literales heredados (baseline ${BASELINE_TAMANOS}).`);
