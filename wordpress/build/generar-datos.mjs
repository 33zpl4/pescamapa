// One-off generators for the WordPress plugin's static data:
//
//   node --experimental-strip-types wordpress/build/generar-datos.mjs
//
//   data/ui.json   — interface strings, language metadata and chip labels,
//                    ported from src/i18n/ui.ts. From now on ui.json is the
//                    source; edit it directly.
//   data/hero.json — Argentina outline, neighbours and rivers as SVG paths,
//                    plus a lat/lon → x/y grid so PHP places pins with the
//                    exact projection used to draw the outline.
import { readFileSync, writeFileSync } from 'node:fs';
import { feature } from 'topojson-client';
import { geoPath, geoTransverseMercator } from 'd3-geo';
import { IDIOMAS, META, ETIQUETAS, traductor } from '../../src/i18n/ui.ts';

const world = JSON.parse(readFileSync(new URL('../../node_modules/world-atlas/countries-50m.json', import.meta.url), 'utf8'));
const DATA = new URL('../plugins/pescamapa-core/data/', import.meta.url);

// --- ui.json ---------------------------------------------------------------
const fuente = readFileSync(new URL('../../src/i18n/ui.ts', import.meta.url), 'utf8');
const bloqueEs = fuente.slice(fuente.indexOf('const es = {'), fuente.indexOf('type Claves'));
const claves = [...bloqueEs.matchAll(/'([a-z0-9.]+)':/g)].map((m) => m[1]);
const ui = { idiomas: IDIOMAS, meta: META, etiquetas: ETIQUETAS, textos: {} };
for (const i of IDIOMAS) {
	const t = traductor(i);
	// {lugar} placeholders become %lugar% for PHP strtr().
	ui.textos[i] = Object.fromEntries(claves.map((k) => [k, t(k, { lugar: '%lugar%' })]));
}
writeFileSync(new URL('ui.json', DATA), JSON.stringify(ui, null, '\t') + '\n');
console.log(`ui.json: ${claves.length} claves × ${IDIOMAS.length} idiomas`);

// --- hero.json -------------------------------------------------------------
const W = 420;
const H = 640;
const todos = feature(world, world.objects.countries);
const pais = (ids) => ({ type: 'FeatureCollection', features: todos.features.filter((f) => ids.includes(String(f.id))) });
const proj = geoTransverseMercator().rotate([64, 0]).fitExtent([[24, 12], [W - 24, H - 12]], pais(['032']));
const path = geoPath(proj);
const redondear = (d) => d.replace(/(\d+\.\d{2})\d+/g, '$1');

const RIOS = [
	[[-25.6, -54.6], [-27.37, -55.9], [-27.58, -56.68], [-27.47, -58.83], [-28.5, -59.05], [-29.14, -59.3], [-30.0, -59.55], [-30.74, -59.65], [-31.73, -60.52], [-32.95, -60.64], [-33.68, -59.66], [-34.2, -58.9], [-34.6, -58.2]],
	[[-27.5, -55.5], [-28.6, -56.0], [-29.7, -57.1], [-30.3, -57.6], [-31.2, -57.93], [-32.22, -58.13], [-33.1, -58.4], [-34.0, -58.4]],
	[[-26.3, -58.2], [-27.0, -58.4], [-27.47, -58.83]],
];

// Grid every 0.5° over Argentina's bounding box: PHP interpolates inside a cell.
const grid = { lat0: -56, lat1: -21, lon0: -74, lon1: -53, paso: 0.5, x: [], y: [] };
for (let la = grid.lat0; la <= grid.lat1 + 1e-9; la += grid.paso) {
	const fx = [];
	const fy = [];
	for (let lo = grid.lon0; lo <= grid.lon1 + 1e-9; lo += grid.paso) {
		const [x, y] = proj([lo, la]);
		fx.push(Math.round(x * 100) / 100);
		fy.push(Math.round(y * 100) / 100);
	}
	grid.x.push(fx);
	grid.y.push(fy);
}

const hero = {
	ancho: W,
	alto: H,
	pais: redondear(path(pais(['032', '238']))),
	vecinos: redondear(path(pais(['152', '858', '600', '068', '076']))),
	rios: RIOS.map((r) => redondear(path({ type: 'LineString', coordinates: r.map(([la, lo]) => [lo, la]) }))),
	grid,
};
writeFileSync(new URL('hero.json', DATA), JSON.stringify(hero) + '\n');
console.log(`hero.json: ${(JSON.stringify(hero).length / 1024).toFixed(0)} KB; Goya → ${proj([-59.26, -29.14]).map((n) => n.toFixed(1))}`);
