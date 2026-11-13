# Pendientes y mejoras (al 4/11/2026)

Lista única de lo que hay que completar y de lo que conviene mejorar, ordenada por área. Reúne lo que dejó abierta la sesión del 4/11/2026 (Patagonia, temporada, correo, PESQA) y remite a la lista anterior de `docs/estado.md` ("Pendientes, en orden") para lo del Litoral y de las primeras semanas. **Quién:** *Claude* lo puede hacer solo; *Dueño* necesita una acción suya (abrir una página en el navegador, decidir, contactar); *Ambos* si hace falta una decisión y luego trabajo. **Prioridad:** 1 = esta semana, 2 = próximas dos semanas, 3 = cuando haya tiempo o tráfico.

Cómo se trabaja cada ítem: `docs/nueva-ciudad.md` (guías) y `docs/prestadores.md` (fichas). La prueba de "hecho" es la corrida en verde y la lectura en vivo con `investigar.yml`.

## 1. Patagonia: contenido por completar

| # | Qué falta | Por qué importa | Quién | Pri. |
| --- | --- | --- | --- | --- |
| 1.1 | **Anexo de Parques Nacionales 2026/27** (Los Alerces: lagos Futalaufquen, Verde, Menéndez y Arrayanes; Lago Puelo). Primer intento: leer `reglamentodepesca.org.ar/reglamento-items/ambientes-parques-nacionales-del-sur/` con `investigar.yml` en **modo navegador** (el Anexo Chubut abrió así). Si no abre, el dueño pega el texto | La guía de Esquel marca esos lagos como pendientes; sin eso no publicamos sus reglas | Claude, luego Dueño | 1 |
| 1.2 | **Ambientes de Río Negro** (Limay río negrino, Ñirihuau, Pichileufu) y texto general 2026/27 (arts. 4 y 22). Probar primero el modo navegador, igual que 1.1 | Cierra Bariloche; hoy dice "no leímos" | Claude, luego Dueño | 1 |
| 1.3 | **Reglamento general de Santa Cruz** (suelas de fieltro, anzuelos, desinfección) y el Anexo I completo con sus líneas de exclusión | La guía del Gallegos publica solo lo del anexo de ambientes | Claude (portal oficial abrió el 4/11) | 2 |
| 1.4 | **Lodges cuyo sitio no se pudo leer**: Estancia María Behety, La Villa y Despedida (Tierra del Fuego); Bella Sofía, Los Plateados, Truchaike, Los Morros y El Rincón (Santa Cruz); Altos del Molino y Patagonia River Guides (403), Lago Rosario Lodge (robots.txt) y Cipreses (pantalla de espera) en Chubut. Reintentar con modo navegador; si no, pedir a cada uno los datos | Fichas con tarifa fechada son lo que nadie más tiene | Claude | 2 |
| 1.5 | **Tarifas 2026/27 publicadas por el operador** de los lodges de Chubut, Tierra del Fuego (San José, AHO, Wuijoor) y Santa Cruz (Karku, Las Buitreras). Hoy solo Kau Tapen publica. **Las Buitreras: hecho el 13/11/2026** (programa 2027 que mandó el operador; falta Karku). Revisar sus páginas de tarifas o preguntarles | La página de costo y comparación de lodges depende de esto | Ambos | 2 |
| 1.6 | **Calendario por mes con una fuente técnica** en las seis guías (eclosiones, caudales, mejores semanas). Hoy cada guía tiene una fila `PENDIENTE` | La barra de meses solo muestra la norma y una opinión atribuida | Claude + fuente | 3 |
| 1.7 | **Alturas del río y clima** en las guías sin estación (Open-Meteo para el clima). Hoy Patagonia no tiene el bloque "Cómo está el río hoy" | Igual que en el Litoral, es lo que posiciona (página de altura del río) | Claude | 3 |
| 1.8 | **Fotos con crédito**: Esquel y Trevelin no tiene portada; Bariloche, Junín y Jurassic ya la tienen. Pasarlas con el nombre del autor | Las guías sin foto se ven vacías en la tarjeta del inicio | Dueño | 2 |
| 1.9 | **Guías independientes**: Cámara de Guías de Neuquén, registro de Parques Nacionales (Nahuel Huapi), registro de prestadores de Neuquén (201 al 4/11), Turismo Trevelin y la lista de Santa Cruz (hoja `Directorios` del Excel del dueño). Cargar como "listado con información pública", solo lo leído | Es la cola larga de consultas de guías de día | Claude | 3 |
| 1.10 | **Guía del Limay Medio** (Neuquén): Limay River Lodge (hoy oculto), Fario Fishing Lodge, Limay River Ranch, RIVUS. Cuando exista, Limay River Lodge vuelve a publicarse | Limay River Lodge estaba mal ubicada en Bariloche | Claude | 3 |
| 1.11 | **Otros destinos de la hoja del Excel**: Villa La Angostura, Lago Puelo y El Bolsón, Aluminé y Pulmarí, Río Pico, Tierra del Fuego sur (Ushuaia, Fagnano). Elegir por consultas y por lodges | Amplía la cobertura de Patagonia | Ambos | 3 |
| 1.12 | **Lectura detallada de los operadores de Chubut que abrieron** (Baqueanos, Rios Grandes, Patagonia Trout Outfitters, AHO Esquel, Pescando en el Paraíso): solo se leyeron por encima | Pueden publicar programas o tarifas | Claude | 3 |

## 2. Producto y sitio

| # | Qué falta | Quién | Pri. |
| --- | --- | --- | --- |
| 2.1 | **Vencimiento de temporadas.** Las fechas cargadas son de 2026/27 y vencen entre el 18/4/2027 (Río Grande) y el 30/9/2027. Hacer un script de solo lectura (como `fichas-por-revisar`) que liste las guías con `temporada_cierra` pasada o a menos de 30 días, y repetir cada octubre la carga con la norma nueva. Hasta que se cargue la nueva, la pregunta responde "la temporada terminó" y no inventa | Claude | 2 |
| 2.2 | **Fechas del Litoral.** Las guías de Corrientes, Entre Ríos y Chascomús no tienen ventana oficial leída y no muestran barra. Leer la norma de cada provincia y cargar apertura y cierre solo donde exista (Chascomús tiene veda del 1/9 al 30/11 en su guía); donde el agua está abierta todo el año, decidir cómo mostrarlo | Ambos | 2 |
| 2.3 | **La página `/calendario-de-pesca/` y `llms.txt` usan el texto libre** de cada guía. Pasarlos a las fechas estructuradas (abre, cierra, pico atribuido) y mostrar la barra de meses por destino | Claude | 2 |
| 2.4 | **Página por ciudad y hub de provincia** con la barra de meses y la pregunta "¿se puede pescar ahora?" por destino | Claude | 3 |
| 2.5 | **Hub por región** (`/patagonia/`, `/litoral/`): hoy solo existen los hubs de provincia | Claude | 3 |
| 2.6 | **Páginas de especie por provincia** ("trucha marrón en Santa Cruz") cuando haya guías suficientes; y fichas de especie que faltan (pacú, patí, boga, tararira, trucha, pejerrey) | Claude | 3 |
| 2.7 | **"Cerca de acá"** entre guías cercanas | Claude | 3 |
| 2.8 | **Página de costo y comparación de lodges de Patagonia** ("cuánto cuesta un viaje a Patagonia"), solo con tarifas que el propio operador publica con su temporada. Depende de 1.4 y 1.5 | Claude | 2 |
| 2.9 | **Ficha de prestador como página de producto** (idea P12, URL propia): decisión del dueño pendiente | Dueño | 3 |
| 2.10 | **Nota por estación de río** ("altura del río en Zárate hoy, ¿se puede pescar?") solo si el bloque "Qué significa para pescar" no alcanza para convertir (P13). Medir a los 30 días | Claude | 3 |
| 2.11 | **Esquina y Eventos con URL propia**: traducciones pendientes de la lista anterior y eventos (Fiesta del Surubí, del Pacú, del Dorado) | Claude | 3 |

## 3. Prestadores y negocio

| # | Qué falta | Quién | Pri. |
| --- | --- | --- | --- |
| 3.1 | **Un contacto por región** (Patagonia norte, Patagonia sur, Litoral) para poder responder la primera consulta en 24 horas: pedir confirmación de datos, tarifa fechada, tres fotos propias y el acuerdo de comisión en paquetes | Dueño | 1 |
| 3.2 | **Primer operador con acuerdo**: abre el nivel 2 (oferta, JSON-LD de oferta). Hoy ninguno | Dueño | 2 |
| 3.3 | **Oferta de sitio web a lodges** (revamp a cambio de relación, no de enlace): piloto con dos o tres lodges, con paridad de precios y sitio a nombre del lodge. Detalle y cautelas en la conversación del 4/11/2026; no es un trueque de enlaces | Ambos | 3 |
| 3.4 | **Campaña de correos a guías y lodges** cuando haya tráfico que mostrarles (impresiones sobre su ficha) | Dueño | 3 |
| 3.5 | **Consulta legal** sobre el término "agencia" y sobre vender paquetes ajenos (Bitácora de Viajes puede servir de agencia) | Dueño | 2 |
| 3.6 | **Reemplazar los datos de ejemplo** de los prestadores del Litoral y revisar las fichas a los 90 días (`fichas-por-revisar`, hoy 32 al día) | Ambos | 2 |
| 3.7 | **Primeras entrevistas** (Agus y su papá, Goya): permiso para nombre, foto y frases | Dueño | 3 |

## 4. Medición y SEO

| # | Qué falta | Quién | Pri. |
| --- | --- | --- | --- |
| 4.1 | **Correr las 20 preguntas de `docs/preguntas-llm.md`** en ChatGPT, Gemini y Perplexity y anotar a quién citan. Única medición directa; la línea base del 4/11 fue una búsqueda web, no los asistentes | Dueño | 1 |
| 4.2 | **Repetir las preguntas cada mes** y anotar el cambio | Dueño | 2 |
| 4.3 | **Pedir indexación** de las URLs nuevas (cuatro guías de Patagonia en tres idiomas, el calendario, la página para guías, los hubs de provincia) en Search Console | Dueño | 1 |
| 4.4 | **Informe semanal:** el cron nunca disparó (0 corridas programadas hasta el 4/11). Se movió a lunes 13:23 UTC; revisar el lunes 9/11 y, si no corre, dispararlo a mano | Claude | 1 |
| 4.5 | **Medir a los 30 días (hacia el 1/12/2026)** si las páginas de altura del río derivan clics a guías y al formulario | Ambos | 2 |
| 4.6 | **Ronda 3 de Keyword Planner** (lotes 5 y 6, ES y PT) para el hub de Corrientes y las búsquedas regionales | Dueño | 2 |
| 4.7 | **Vigilar PESQA**: si actualiza su temporada 2026/27 y si los asistentes la citan. Si lo hace, nuestra ventaja de fechas se acorta y quedan precios fechados y lectura de la norma | Claude | 3 |
| 4.8 | **verify-live:** la corrida programada del 26/10 falló en "Served pages" y no se investigó la causa; revisar y agregar las guías nuevas | Claude | 2 |

## 5. Correo y formularios

| # | Qué falta | Quién | Pri. |
| --- | --- | --- | --- |
| 5.1 | **Probar el formulario real de punta a punta** (guardado en Consultas, aviso por Resend, respuesta). El envío de prueba ya funcionó; el formulario no se probó | Dueño | 1 |
| 5.2 | **Probar el formulario de "Para guías y lodges"** igual | Dueño | 2 |
| 5.3 | **Responder desde Gmail como info@pescamapa.com** con las credenciales SMTP de Resend (opcional) | Dueño | 3 |
| 5.4 | **DMARC:** hoy el destino de reportes es el de GoDaddy por defecto; poner uno propio si se quiere leerlos | Dueño | 3 |

## 6. Operación y técnica (lo aprendido)

- **Los agentes de búsqueda del entorno no abren páginas** (ENOTFOUND). Toda lectura de fuentes va por `investigar.yml`. Si un sitio da 403 o "fetch failed", repetir con `navegador: true`. El texto capturado se corta a 12.000 caracteres: para listas largas leer el `.html`. Si una corrida sale "cancelled", se pisó con otra: repetirla.
- **Un `wp-script` a la vez.** Se puede lanzar desde una rama `claude/*` antes del merge; simulacro, leer el log, aplicar, `VERIFIED`.
- **En PHP, `«$variable»` se lee mal:** usar `«{$variable}»` (nos pasó dos veces).
- **La rama extra `claude/email-diagnostico-20261104` sigue en el remoto** (el borrado falló dos veces); borrarla a mano o con la limpieza de ramas.
- **Fechas dentro de la respuesta calculada:** una página en caché queda vieja, no equivocada. Si algún día se agrega caché agresiva, revisar el tiempo de vida.
- **Informes de agentes:** pedirles siempre que, si no pueden abrir páginas, lo digan en la primera línea y no entreguen un informe armado con resúmenes de buscador.

## 7. Decisiones del dueño ya tomadas (no relitigar)

- La "mejor época" se muestra solo si una fuente con nombre la afirma; la temporada legal sale solo de la norma (4/11/2026).
- Precios solo con la fecha en que los publica el propio operador; sin precios de agencias (`docs/prestadores.md`).
- Sin fotos descargadas de los sitios de los guías ni enlace a su Instagram en la ficha (1/11/2026).
- La licencia de las fotos las aporta el dueño con crédito; no se verifica caso por caso (`docs/contenido.md`).

### Páginas de precios (5/11/2026)
- Hambaré: temporada de sus tarifas (las de 3d/4d para grupos de 3 ya están: US$ 855 y US$ 1.105, leídas el 5/11/2026).
- Leer las tarifas de Karandá y Delta Lodge (Nervous Waters), Pirá "Rates 2025-26", Villa María y Despedida Lodge; micros y vuelos.
- **Revisado el 6/11/2026 y no publicado en ningún sitio que se pueda citar** (no se completa sin inventar): salidas con precio en Paso de la Patria y en Goya (Pescar: "todavía no hay salidas publicadas"; Careca Pesca solo tiene un paquete de torneo de agosto 2026, vencido; un portal turístico da rangos sin prestador); valor de la licencia de Corrientes (la página del Ministerio de Turismo no muestra monto y el portal de licencias carga con JavaScript sin texto). Para estos hay que pedírselos a un prestador o a Recursos Naturales; ChatGPT dio un $ 350.000 de Pescar que no existe.
- Temporada de las tarifas de Hambaré: preguntársela a la posada (WhatsApp +54 9 3777 622477, número público de su sitio).
- Medición día 14 (19/11) y día 30 (5/12): `node scripts/consultar-ia.mjs`, mismas preguntas, carpeta `docs/investigacion/medicion-ia/`. Gemini con búsqueda: cargar los secretos del repo `GEMINI_API_KEY` y `OPENAI_API_KEY` y correr `medir-ia.yml` (semanal, lunes 14:41 UTC); la clave de Gemini de la capa gratuita se agota (429) tras unas 45 llamadas por día, así que para medir con regularidad hay que activar facturación en la cuenta de Google. Tablas de las notas: ya tienen sombra de desplazamiento (6/11/2026).
- Verificar con una captura que las tablas se ven bien en el celular (workflow `capturas.yml`).
- Reemplazar el dato público por datos propios cuando haya acuerdos con prestadores (fila por fila en el JSON).
- **Bariloche (6/11/2026):** la nota ya va por niveles (medio día US$ 410; día completo desde US$ 500, US$ 530 o desde US$ 545; Manso US$ 600; $ 666.000 en pesos). Falta: tarifa de los programas de varios días y flotadas con campamento (Outfitters Patagonia y Mato publican "consultar"), tarifas de Panil (su sitio no abrió), y una tarifa premium publicada por encima de US$ 600 (no la encontramos: el rango US$ 550–700 de Gemini no tiene fuente). Si aparecen, agregar fila por fila en `wordpress/contenido/precios/paginas.es.json` y regenerar con `precios-a-script.mjs --actualizar --solo=precio-pesca-bariloche-limay`.
- **Pesca desde Brasil (6/11/2026): página en vivo** (`/notas/pesca-en-corrientes-desde-brasil/`, `/pt/notas/pesca-em-corrientes-desde-brasil/`, `/en/notes/fishing-in-corrientes-from-brazil/`): licencia de turista extranjero por litros de nafta (Disposición 1144/2024), valores aproximados con nafta a $ 2.177/L (promedio de 14 estaciones de Corrientes, Secretaría de Energía, 21/10/2026), registro del portal con CPF brasileño, documentos de ingreso, rutas de Google Maps (Uruguaiana → Esquina 310 km / 4 h 31; Paso de los Libres → Esquina 307 km / 4 h 22; São Borja → Paso de la Patria 386 km / 4 h 45) y el paso Santo Tomé–São Borja (24 h). **Para actualizar:** el precio de la nafta (cada vez que se revise, con su fecha) y los valores aproximados de la tabla (litros × precio). **Falta:** aduana (auto propio, equipo, carnada), medios de pago del portal para un extranjero, ruta Uruguaiana → Goya, precios en reales de operadores brasileños, horarios y esperas de cada paso, paso Paso de los Libres–Uruguaiana (no encontramos su página oficial), y confirmar con Migraciones los documentos.

