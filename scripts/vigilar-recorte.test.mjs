// node scripts/vigilar-recorte.test.mjs
import assert from 'node:assert/strict';
import { recortar } from './vigilar-recorte.mjs';

const pagina = 'Inicio Menú Contador 1234 visitas. Veda del dorado desde el 1 de septiembre. Obras de defensa en la ribera. Torneo de pesca en marzo. Concurso de fotos de barrio.';
const f = { contiene: ['veda', 'torneo de pesca'] };
const r = recortar(pagina, f);
assert.equal(r, 'Veda del dorado desde el 1 de septiembre. ¶ Torneo de pesca en marzo.');
assert.equal(recortar(r, f), r, 'idempotente');
// Noise in the dropped sentences does not change the result.
assert.equal(recortar(pagina.replace('Obras de defensa', 'Otras obras nuevas'), f), r);
// A relevant change does.
assert.notEqual(recortar(pagina.replace('1 de septiembre', '15 de octubre'), f), r);

assert.equal(recortar('a b INICIO x y FIN z', { desde: 'inicio', hasta: 'fin' }), 'INICIO x y');
assert.equal(recortar('a b INICIO x y', { desde: 'nada' }), 'a b INICIO x y', 'ancla ausente: no recorta');
assert.equal(recortar('Visitas 12 345 hoy y veda', { ignorar: ['visitas [\\d ]+'] }), 'hoy y veda');
assert.equal(recortar('  igual   texto ', {}), 'igual texto');
console.log('vigilar-recorte: ok');
