// Bite-zone map (inc/pique.php, docs/pique.md). The position is snapped to a
// ~1 km grid cell HERE; only the integer cell indexes are ever sent.
(function () {
	var el = document.querySelector('.pique-mapa');
	if (!el || !window.L) return;
	var PASO = 100; // cells per degree: 0.01° ≈ 1 km
	var T = JSON.parse(el.dataset.textos);
	var destinos = JSON.parse(el.dataset.destinos || '[]');
	var map = L.map(el, { scrollWheelZoom: false });
	// Opening view: where our guides are (never the whole country); the visitor's own location replaces it below.
	if (destinos.length) map.fitBounds(L.latLngBounds(destinos.map(function (d) { return d.coordenadas; })).pad(0.4), { maxZoom: 8 });
	else map.setView([-30, -59], 6);
	L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
		maxZoom: 18,
		attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
	}).addTo(map);

	function bounds(lat, lon) { return [[lat / PASO, lon / PASO], [(lat + 1) / PASO, (lon + 1) / PASO]]; }
	function centro(lat, lon) { return [(lat + 0.5) / PASO, (lon + 0.5) / PASO]; }
	function fmt(s, v) { return Object.keys(v).reduce(function (a, k) { return a.replace('%' + k + '%', v[k]); }, s); }

	var circulos = [];
	JSON.parse(el.dataset.celdas || '[]').forEach(function (c) {
		var nada = c.bueno === 0;
		var color = nada ? '#7d8a7a' : '#ee7a48';
		var op = Math.min(0.35 + c.n * 0.12, 0.9);
		var rect = L.rectangle(bounds(c.lat, c.lon), { color: color, weight: 1, fillColor: color, fillOpacity: op * 0.6 }).addTo(map);
		var dot = L.circleMarker(centro(c.lat, c.lon), { radius: 10, color: '#fbfaf6', weight: 3, fillColor: color, fillOpacity: op }).addTo(map);
		circulos.push(dot);
		var t = document.createElement('div');
		var b = document.createElement('strong');
		b.textContent = c.n === 1 ? T['popup.reporte1'] : fmt(T['popup.reportes'], { n: c.n });
		t.append(b, document.createElement('br'), c.especies.map(function (e) { return e[0] + ' ' + e[1]; }).join(' · '));
		t.append(document.createElement('br'), fmt(T['popup.bueno'], { n: c.bueno }), document.createElement('br'), fmt(T['popup.ultimo'], { fecha: c.ultimo }));
		rect.bindPopup(t); dot.bindPopup(t);
	});
	// At street zoom the real cell shows; the dot would overstate precision.
	function ajustar() { circulos.forEach(function (d) { d.setStyle({ opacity: map.getZoom() >= 12 ? 0 : 1, fillOpacity: map.getZoom() >= 12 ? 0 : d.options.fillOpacity }); }); }
	map.on('zoomend', ajustar);

	// Jump to a destination (so nobody has to find Goya on the map).
	if (destinos.length) {
		var ir = document.createElement('label');
		ir.className = 'pique-ir';
		ir.append(T.ir + ' ');
		var sl = document.createElement('select');
		var o0 = new Option(T['ir.elegi'], '');
		sl.append(o0);
		destinos.forEach(function (d, i) { sl.append(new Option(d.nombre, String(i))); });
		sl.addEventListener('change', function () { if (sl.value !== '') map.setView(destinos[Number(sl.value)].coordenadas, 11); });
		ir.append(sl);
		el.parentNode.insertBefore(ir, el);
	}
	// The visitor's location, if the browser allows it: only the map moves, nothing is sent or chosen.
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function (p) {
			map.setView([p.coords.latitude, p.coords.longitude], 11);
		}, function () {}, { enableHighAccuracy: false, timeout: 8000, maximumAge: 600000 });
	}

	// Report form
	var form = document.querySelector('.pique-form form');
	var zona = document.querySelector('.pique-zona');
	var aviso = document.querySelector('.pique-form .aviso');
	var enviar = form.querySelector('button[type=submit]');
	var sel = null;
	function elegir(lat, lon) { // lat/lon in degrees, snapped right here
		var la = Math.floor(lat * PASO), lo = Math.floor(lon * PASO);
		form.celda_lat.value = la; form.celda_lon.value = lo;
		if (sel) sel.remove();
		sel = L.rectangle(bounds(la, lo), { color: '#1f3a2f', weight: 2, dashArray: '5 4', fillOpacity: 0.15 }).addTo(map);
		zona.textContent = fmt(T.zona, { zona: (la / PASO).toFixed(2) + ', ' + (lo / PASO).toFixed(2) });
		enviar.disabled = false;
	}
	map.on('click', function (e) { elegir(e.latlng.lat, e.latlng.lng); });
	document.querySelector('.pique-geo').addEventListener('click', function () {
		if (!navigator.geolocation) { zona.textContent = T.sinubicacion; return; }
		navigator.geolocation.getCurrentPosition(function (p) {
			elegir(p.coords.latitude, p.coords.longitude);
			map.setView(centro(Math.floor(p.coords.latitude * PASO), Math.floor(p.coords.longitude * PASO)), 12);
		}, function () { zona.textContent = T.sinubicacion; }, { enableHighAccuracy: false, timeout: 10000 });
	});
	form.addEventListener('submit', function (ev) {
		ev.preventDefault();
		enviar.disabled = true;
		fetch(el.dataset.envio, { method: 'POST', body: new FormData(form), credentials: 'same-origin' })
			.then(function (r) { return r.json(); })
			.then(function (j) {
				aviso.hidden = false;
				aviso.className = 'aviso ' + (j.ok ? 'ok' : 'error');
				aviso.textContent = j.ok ? T.gracias : (j.error === 'limite' ? T.limite : T.error);
				if (j.ok) { if (sel) sel.remove(); sel = null; form.celda_lat.value = ''; form.celda_lon.value = ''; zona.textContent = zona.dataset.vacio; } else { enviar.disabled = false; }
			})
			.catch(function () { aviso.hidden = false; aviso.className = 'aviso error'; aviso.textContent = T.error; enviar.disabled = false; });
	});
})();
