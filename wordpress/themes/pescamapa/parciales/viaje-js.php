<?php
/**
 * Niceties for the trip-request forms (.pm-form). Everything works without this script:
 * with JS off the fields are plain inputs, the month chips are always visible and both contact fields show.
 * Used once per page (the long form and the short card never share a page).
 */
defined( 'ABSPATH' ) || exit;
?>
<script>
document.querySelectorAll('form.pm-form').forEach((f) => {
	f.classList.add('js');
	// Steppers: - / + around the number inputs (the input still works by typing and with the keyboard arrows).
	f.querySelectorAll('.stepper').forEach((s) => {
		const i = s.querySelector('input');
		const mk = (txt, label, d) => {
			const b = document.createElement('button');
			b.type = 'button'; b.textContent = txt; b.setAttribute('aria-label', label);
			b.addEventListener('click', () => {
				const min = +i.min || 0, max = +i.max || 99;
				i.value = Math.max(min, Math.min(max, (parseInt(i.value, 10) || min) + d));
				i.dispatchEvent(new Event('change', { bubbles: true }));
			});
			return b;
		};
		s.insertBefore(mk('−', s.dataset.menos, -1), i);
		s.appendChild(mk('+', s.dataset.mas, 1));
	});
	// "These also work for me": the destination chosen above is not offered again (the server drops it anyway).
	const sel = f.querySelector('#viaje-destino'), tam = f.querySelector('[data-tambien]');
	if (sel && tam) {
		const ver = () => tam.querySelectorAll('[data-clave]').forEach((s) => {
			const igual = s.dataset.clave === sel.value;
			s.hidden = igual;
			if (igual) s.querySelector('input').checked = false;
		});
		sel.addEventListener('change', ver); ver();
	}
	// Dates: "to" never before "from".
	const desde = f.querySelector('[name="fecha_desde"]'), hasta = f.querySelector('[name="fecha_hasta"]');
	if (desde && hasta) {
		const sync = () => {
			hasta.min = desde.value || desde.min;
			if (desde.value && hasta.value && hasta.value < desde.value) hasta.value = '';
		};
		desde.addEventListener('change', sync); sync();
	}
	// "I do not know yet": months instead of dates.
	const bloque = f.querySelector('[data-fechas]'), meses = f.querySelector('[data-meses]');
	if (bloque && meses) {
		const exactas = bloque.querySelector('.fechas-exactas');
		const ver = () => {
			const nose = !!f.querySelector('[name="flexibilidad"][value="nose"]:checked');
			const conMes = !!meses.querySelector('input:checked');
			meses.hidden = !(nose || conMes);
			exactas.hidden = nose;
		};
		f.querySelectorAll('[name="flexibilidad"]').forEach((r) => r.addEventListener('change', ver)); ver();
	}
	// Optional radio chips: tapping the chosen one again clears it.
	f.querySelectorAll('.chips-radio input[type="radio"]').forEach((r) => {
		if (r.name === 'contacto_via') return; // that choice always has a value
		let estaba = false;
		r.addEventListener('pointerdown', () => { estaba = r.checked; });
		r.addEventListener('click', () => { if (estaba) { r.checked = false; r.dispatchEvent(new Event('change', { bubbles: true })); } estaba = false; });
	});
	// Contact: the chosen channel is the required one (the other is hidden by CSS).
	const via = f.querySelectorAll('[name="contacto_via"]'), em = f.querySelector('[name="contacto_email"]'), tel = f.querySelector('[name="contacto_tel"]');
	if (via.length && em && tel) {
		const act = () => { const w = f.querySelector('[name="contacto_via"]:checked'); const wa = w && w.value === 'whatsapp'; tel.required = wa; em.required = !wa; };
		via.forEach((r) => r.addEventListener('change', act)); act();
	}
	// After a validation error, take the person to the first field to fix.
	const mal = f.querySelector('[aria-invalid="true"]');
	if (mal) { mal.scrollIntoView({ block: 'center' }); mal.focus({ preventScroll: true }); }
	// One tap, one request: the hourly limit would otherwise count a double click twice.
	f.addEventListener('submit', () => {
		const b = f.querySelector('button[type="submit"]');
		if (b && f.dataset.enviando) { b.disabled = true; b.textContent = f.dataset.enviando; }
	});
});
</script>
