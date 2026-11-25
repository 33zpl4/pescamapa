# Páginas pensadas para la persona: la respuesta primero, la fuente detrás

> Regla del dueño, 24/11/2026, a raíz del calendario de pesca. Vale para toda página y herramienta nueva, y para
> las que se rehagan. Leer antes de diseñar una página.

## El objetivo (dueño, 25/11/2026)

> «Nuestro objetivo de verdad tiene que ser: hacerle las cosas más fáciles y cómodas a quien planifica su viaje… más eficiente, más rápido (que le lleve menos tiempo), mejor experiencia… y entonces vamos a tener éxito.»

Hacerle las cosas más fáciles y cómodas a quien planifica su viaje de pesca: más eficiente, con menos tiempo y mejor experiencia. La escala la decide el mercado: no se persigue como meta, llega si lo anterior se cumple. **Prueba para cualquier página o herramienta: «¿esto le ahorra tiempo o pasos a quien planifica?»** Si la respuesta es no (o solo nos sirve a nosotros), se acorta, se reordena o se saca. Por eso una nota que compara caminos dice cuándo otro camino es mejor que el nuestro.

## Qué pasó

`/calendario-de-pesca/` mostraba, destino por destino, lo que dice cada fuente: «Su página dice "octubre a mayo"…»,
«En un artículo de octubre de 2024…», «Entre las dos fuentes no coinciden…», más el resumen del reglamento y la
autoridad en cada bloque. Unas 24.000 px de alto en el móvil. El dueño, al verla en el teléfono: «no está pensado
para una persona de ninguna manera». Quien entra quiere saber cuándo ir; recibía nuestro cuaderno de investigación.

## La regla

**El visitante quiere la respuesta, dada por nosotros.** La construimos con fuentes, pero no las exponemos ni las
citamos línea por línea en la lectura. Las fuentes sirven para tres cosas (tener el dato correcto, mantenerlo al día
y que un asistente o un buscador confíe en la página) y ninguna de las tres exige ponerlas delante del lector.

1. **Una sola lectura por dato.** Si dos fuentes no coinciden, decidimos nosotros y mostramos una respuesta. La
   discrepancia va en el desplegable de fuentes, nunca como fila de la página («Entre las dos fuentes…» no se publica
   en el cuerpo).
2. **Las fuentes, plegadas.** Un desplegable por tarjeta o por sección («De dónde sale · revisado el …») con
   autoridad, enlace y fecha. Siguen en el HTML (los leen buscadores y asistentes) y en la guía del destino. No se
   nombran prestadores ni se enlazan sus páginas en el cuerpo de una respuesta.
3. **Empezar por la pregunta del visitante.** Una página de consulta abre con un filtro que es su pregunta («¿Dónde
   querés pescar?», «¿Qué querés pescar?»), no con un índice de nuestras secciones.
4. **Mostrar antes que contar.** Lo que tiene forma (meses, rangos, niveles, lugares) se dibuja: barra de 12 meses,
   mapa, escala. Un párrafo que describe un rango de meses es señal de que falta un dato estructurado.
5. **Una tarjeta, una respuesta.** Por resultado: el estado de hoy en una etiqueta, el gráfico, una frase corta con
   nuestra lectura y la acción (ver la guía, pedir opciones gratis). El detalle largo vive en la guía del destino.
6. **Nada de desplazamiento infinito.** Si la página necesita listar muchos elementos, se filtra; no se apila todo.
7. **Si falta el dato, se dice corto.** «Veda: fecha por confirmar», no la del año pasado ni un párrafo que explica
   qué no pudimos leer.
8. **Se diseña en el móvil y se muestra antes de construir.** Una maqueta (captura) al dueño antes de delegar o
   programar: es barata y evita rehacer. Ya había pasado con la nota del Paraná, rehecha tres veces.

## Cómo comprobarlo antes de publicar

- ¿La primera pantalla del móvil responde algo o pide elegir algo? Si solo presenta, falta.
- ¿Hay alguna frase del cuerpo que empiece por «Su página dice», «Según…», «La fuente…»? Va al desplegable.
- ¿Hay nombres de prestadores o enlaces externos en el cuerpo de la respuesta? Fuera.
- ¿Cuánto mide la página en el móvil? Si pasa de unas pocas pantallas sin filtro, falta filtro.
- ¿Algún párrafo describe algo que se podría dibujar? Se convierte en dato y se dibuja.

## Qué no cambia

- El dato sigue necesitando fuente y fecha (`docs/estrategia-contenido.md`, regla de oro); solo cambia dónde se ve.
- Las guías de destino conservan el detalle, el reglamento y sus fuentes.
- Las notas informativas con datos propios (`docs/contenido.md`) sí explican de dónde sale cada cifra: ahí la fuente
  es parte de la historia.

## Primer caso: el calendario (construido el 24/11/2026, rama `claude/confident-feynman-4oj6zh`, sin mergear)

Maqueta aprobada por el dueño y construida: dos filtros (dónde / qué, combinables y reflejados en la URL), una tarjeta por destino con
etiqueta de hoy, barra de 12 meses por especie (mejor época, se pesca, veda, mes de hoy), una frase con nuestra lectura, dos botones y
las fuentes plegadas («De dónde sale · revisado el …»). Salieron de esa página las tablas «época / qué esperar», el resumen del
reglamento, «Temporadas, en una mirada» y «Respuesta corta»; quedan una FAQ corta (con su FAQPage) y el cierre `cta-nota`. El mapa con los
mismos filtros queda para una segunda entrega.

- **Dónde viven los datos**: las fechas oficiales de cada guía (`temporada_abre` / `temporada_cierra`, `pico_meses` + `pico_quien`) y
  `wordpress/plugins/pescamapa-core/data/calendario.json` para el resto. Código: `inc/temporada.php` (`pm_calendario_tarjetas()`),
  `vistas/calendario.php`, `assets/calendario.js`, estilos `.cal-*` en `extra.css`. Detalle y cómo se prueba: `docs/wordpress.md` → «El calendario».
- **Cómo cargar meses o una veda de un destino**: agregar (o editar) la lista de la clave de la guía en `calendario.json`: por especie,
  `mejor`, `pesca`, `veda` (números de mes), `frase` en los tres idiomas, `nota_fuentes` si las fuentes no coinciden, `revisado`. Si la guía
  tiene fechas oficiales, los meses salen de esos campos y el JSON solo aporta la frase y la nota. **Los meses salen solo de lo que la guía ya dice**; si dos fuentes
  no coinciden, decidimos una lectura (pesca = unión, mejor = lo que comparten) y la discrepancia va en `nota_fuentes`, nunca en el cuerpo.
- **Veda**: solo se pinta con las fechas declaradas en la guía y una autoridad (hoy Chascomús, pejerrey, 1/9 al 30/11, y Concordia, dorado, 1/9 al 31/12;
  ambas son vedas anuales fijas, no de esta temporada: **decisión pendiente del dueño**; para quitarlas, `veda: null`). Con `veda: null` la tarjeta dice «Veda: fecha por confirmar».
- **Lo que no hace**: no nombra prestadores ni fuentes en el cuerpo, no escribe «se pesca todo el año» sin matiz, no pinta una veda del año pasado.

**Vedas cargadas el 24/11/2026** (normas que el dueño reunió ese día; van en `nota_fuentes`, plegadas): Corrientes no tiene veda por
fechas (Disposición 853/22: devolución obligatoria de dorado y surubí todo el año; pacú con talla y cupo) y se muestra con el campo
`veda_txt` en vez de «fecha por confirmar»; dorado en el río Uruguay 1/9–31/12 (CARU 111/2023, Concordia); dorado en Entre Ríos
1/9–31/12 (Resolución 687/18, La Paz); pejerrey bonaerense 1/9–30/11 (Disposición 483/2019 modificada por 116/2021). Los cierres
extraordinarios por tramo no se dibujan. Pendiente: la guía de Chascomús cita todavía la Disposición 89/08.
