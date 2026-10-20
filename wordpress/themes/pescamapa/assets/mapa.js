// Interactive maps: every .mapa element carries its pins as data attributes
// (printed by pm_mapa_interactivo). Tiles: OpenStreetMap, with attribution.
(function () {
	var colores = { destino: '#ee7a48', proximo: '#7d8a7a', agua: '#3f7f8c' };
	document.querySelectorAll('.mapa').forEach(function (el) {
		var pins = JSON.parse(el.dataset.pins || '[]');
		var map = L.map(el, { scrollWheelZoom: false }).setView(JSON.parse(el.dataset.centro), Number(el.dataset.zoom));
		L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
			maxZoom: 18,
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
		}).addTo(map);
		pins.forEach(function (p) {
			var m = L.circleMarker(p.coordenadas, {
				radius: p.tipo === 'destino' ? 10 : 7,
				color: '#fbfaf6', weight: 3, fillOpacity: 1,
				fillColor: colores[p.tipo] || colores.proximo,
			}).addTo(map);
			var t = document.createElement('div');
			var b = document.createElement('strong');
			b.textContent = p.nombre;
			t.append(b);
			if (p.detalle) t.append(document.createElement('br'), p.detalle);
			if (p.url) {
				var a = document.createElement('a');
				a.href = p.url;
				a.textContent = el.dataset.guia;
				t.append(document.createElement('br'), a);
			} else if (p.tipo === 'proximo') {
				t.append(document.createElement('br'), el.dataset.pronto);
			}
			m.bindPopup(t);
		});
	});
})();
