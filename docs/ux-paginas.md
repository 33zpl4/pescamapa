# Páginas pensadas para la persona: la respuesta primero, la fuente detrás

> Regla del dueño, 24/11/2026, a raíz del calendario de pesca. Vale para toda página y herramienta nueva, y para
> las que se rehagan. Leer antes de diseñar una página.

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

## Primer caso: el calendario (en curso, 24/11/2026)

Maqueta aprobada por el dueño: dos filtros (dónde / qué), una tarjeta por destino con barra de 12 meses (mejor época,
se pesca, veda, hoy), frase corta, dos botones y fuentes plegadas. Las tablas «época / qué esperar» y el resumen del
reglamento salen de esa página. El mapa con los mismos filtros queda para una segunda entrega. Requiere cargar los
meses por destino y especie como dato (hoy solo 6 destinos de Patagonia tienen fechas).
