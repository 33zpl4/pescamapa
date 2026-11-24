/* Calendar filters (24/11/2026). Progressive enhancement: the server already renders every card (and filters on ?donde=&que=);
   this script turns the chip links into toggle buttons, filters in place, keeps the URL in step, and recomputes "today" in
   Argentina's time zone so a cached page is never wrong. Plain DOM, Baseline widely available (no library). */
(function () {
	'use strict';
	var root = document.getElementById('cal');
	if (!root || !window.URLSearchParams || !window.Intl || !Intl.DateTimeFormat) { return; }
	var TZ = 'America/Argentina/Buenos_Aires';
	var d = root.dataset;
	var lang = d.idioma || 'es';
	var loc = { es: 'es-AR', pt: 'pt-BR', en: 'en-GB' }[lang] || 'es-AR';
	var ESTADO = { m: 'mejor', p: 'pesca', v: 'veda', c: 'cerrado', s: 'sin' };
	var RANGO = { mejor: 4, pesca: 3, veda: 2, cerrado: 1, sin: 0 };
	var CLASE = { mejor: 'mes--pico', pesca: 'mes--abierto', veda: 'mes--veda', cerrado: 'mes--cerrado', sin: 'mes--cerrado' };

	var hoy, mes;
	try {
		hoy = new Intl.DateTimeFormat('en-CA', { timeZone: TZ, year: 'numeric', month: '2-digit', day: '2-digit' }).format(new Date());
		if (!/^\d{4}-\d{2}-\d{2}$/.test(hoy)) { hoy = d.hoy; }
	} catch (e) { hoy = d.hoy; }
	mes = parseInt(hoy.slice(5, 7), 10);
	function fecha(ymd, conAnio) {
		try {
			var o = { timeZone: 'UTC', day: 'numeric', month: 'long' };
			if (conAnio) { o.year = 'numeric'; }
			return new Intl.DateTimeFormat(loc, o).format(new Date(ymd + 'T12:00:00Z'));
		} catch (e) { return ymd; }
	}
	function plantilla(clave, vars) {
		var t = d[clave] || '';
		Object.keys(vars || {}).forEach(function (k) { t = t.split('%' + k + '%').join(vars[k]); });
		return t;
	}

	// Chips: links become buttons (keyboard and aria-pressed for free).
	var estado = { donde: '', que: '' };
	var chips = [];
	root.querySelectorAll('a.cal-chip').forEach(function (a) {
		var b = document.createElement('button');
		b.type = 'button';
		b.className = 'cal-chip';
		b.textContent = a.textContent;
		b.dataset.filtro = a.dataset.filtro;
		b.dataset.valor = a.dataset.valor;
		a.parentNode.replaceChild(b, a);
		chips.push(b);
		b.addEventListener('click', function () {
			estado[b.dataset.filtro] = b.dataset.valor;
			aplicar(true);
		});
	});
	function valido(filtro, v) {
		return chips.some(function (c) { return c.dataset.filtro === filtro && c.dataset.valor === v; });
	}

	var cards = [].slice.call(root.querySelectorAll('.cal-card'));
	var res = document.getElementById('cal-resultados');

	// Recompute every row for today (Argentina) once.
	cards.forEach(function (card) {
		card.querySelectorAll('.cal-esp').forEach(function (row) {
			var cod = row.dataset.meses || '';
			var abre = row.dataset.abre || '', cierra = row.dataset.cierra || '';
			var s = ESTADO[cod.charAt(mes - 1)] || 'sin';
			var fuera = abre && cierra && (hoy < abre || hoy > cierra);
			if (fuera) { s = 'cerrado'; }
			row.dataset.hoy = s;
			row.dataset.pill = textoPill(s, abre, cierra);
			row.querySelectorAll('.mes').forEach(function (li) {
				var actual = parseInt(li.dataset.m, 10) === mes;
				li.classList.toggle('mes--actual', actual);
				if (actual) { li.setAttribute('aria-current', 'date'); } else { li.removeAttribute('aria-current'); }
			});
		});
	});
	function textoPill(s, abre, cierra) {
		if (s === 'cerrado' && abre && hoy < abre) { return plantilla('tAntes', { fecha: fecha(abre, true) }); }
		if (s === 'cerrado' && cierra && hoy > cierra) { return plantilla('tDespues', { fecha: fecha(cierra, true) }); }
		return d['t' + { mejor: 'Mejor', pesca: 'Pesca', veda: 'Veda', cerrado: 'Fuera', sin: 'Sin' }[s]] || '';
	}

	function aplicar(actualizarUrl) {
		var donde = estado.donde, que = estado.que, n = 0, m = 0;
		cards.forEach(function (card) {
			var esp = (card.dataset.especies || '').split(' ');
			var ok = (!donde || card.dataset.region === donde) && (!que || esp.indexOf(que) !== -1);
			card.hidden = !ok;
			card.classList.toggle('cal-card--que', !!que);
			var filas = [].slice.call(card.querySelectorAll('.cal-esp'));
			var visibles = 0, mejor = null;
			filas.forEach(function (row) {
				var ver = !que || row.dataset.esp === que;
				row.hidden = !ver;
				if (ver) { visibles++; }
				if (!mejor || RANGO[row.dataset.hoy] > RANGO[mejor.dataset.hoy]) { mejor = row; }
			});
			var elegida = que ? card.querySelector('.cal-esp[data-esp="' + que + '"]') : mejor;
			var s = elegida ? elegida.dataset.hoy : 'sin';
			var pill = card.querySelector('[data-pill]');
			if (pill) {
				pill.className = 'cal-pill cal-pill--' + s;
				pill.textContent = elegida ? elegida.dataset.pill : d.tSin;
			}
			var sin = card.querySelector('.cal-sin');
			if (sin) { sin.hidden = visibles > 0; }
			var mas = card.querySelector('.cal-mas');
			if (mas) {
				mas.hidden = !!que || filas.length < 2;
				if (que) { card.classList.remove('cal-card--abierta'); }
			}
			if (ok) {
				n++;
				if (s === 'mejor' || s === 'pesca') { m++; }
			}
		});
		chips.forEach(function (c) { c.setAttribute('aria-pressed', estado[c.dataset.filtro] === c.dataset.valor ? 'true' : 'false'); });
		if (res) { res.textContent = resumen(donde, que, n, m); }
		if (actualizarUrl && window.history && history.replaceState) {
			var q = new URLSearchParams();
			if (donde) { q.set('donde', donde); }
			if (que) { q.set('que', que); }
			var s2 = q.toString();
			try { history.replaceState(null, '', location.pathname + (s2 ? '?' + s2 : '')); } catch (e) { /* the view still works */ }
		}
	}
	function nombre(filtro, v) {
		var c = chips.filter(function (x) { return x.dataset.filtro === filtro && x.dataset.valor === v; })[0];
		return c ? c.textContent : '';
	}
	function resumen(donde, que, n, m) {
		if (!n) { return d.rN0; }
		var partes = [];
		if (que) { partes.push(nombre('que', que)); }
		if (donde) { partes.push(nombre('donde', donde)); }
		var cabeza = partes.length ? partes.join(' \u00b7 ') + ': ' : '';
		return cabeza + (n === 1 ? d.rN1 : plantilla('rN', { n: n })) + '. ' + plantilla(m ? 'rHoy' : 'rHoy0', { hoy: fecha(hoy, false), m: m });
	}

	// "Show more species" on the card (only without a species filter).
	root.querySelectorAll('.cal-mas').forEach(function (b) {
		b.addEventListener('click', function () {
			var card = b.closest('.cal-card');
			var abierta = card.classList.toggle('cal-card--abierta');
			b.setAttribute('aria-expanded', abierta ? 'true' : 'false');
			b.textContent = abierta ? b.dataset.menos : b.dataset.mas;
		});
	});

	// Start from the URL (it may differ from what a cached page shows).
	var q0 = new URLSearchParams(location.search);
	estado.donde = valido('donde', q0.get('donde') || '') ? (q0.get('donde') || '') : '';
	estado.que = valido('que', q0.get('que') || '') ? (q0.get('que') || '') : '';
	root.classList.add('cal--js');
	aplicar(false);
})();
