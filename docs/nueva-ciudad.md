# Cómo sumar una ciudad (receta de las 4 guías hechas)

Hechas: Goya, Esquina, Paso de la Patria (Corrientes) y La Paz (Entre Ríos). Modelo a copiar: `wordpress/scripts/la-paz-2026-10-22.php` (otra provincia) y `paso-de-la-patria-2026-10-21.php`.

## Pasos

1. **Investigación** (dueño + Gemini/ChatGPT, que son agentes de búsqueda, no fuente). Le damos la pregunta exacta (ver `docs/investigacion/la-paz.md` §4 como ejemplo). Lo que vuelve se **audita**: la mayoría de los enlaces de esos informes son `google.com/search?q=` o direcciones adivinadas. Nada se publica sin una fuente que una persona (o nosotros) abrió. Se guarda como `docs/investigacion/<ciudad>.md`: qué se usa (con fuente), qué no (y por qué), pendientes.
2. **Provincia**: si es otra provincia, las reglas, la licencia y los contactos son de esa provincia. No copiar nada de Corrientes (el script `licencia-contacto` ya excluye la clave `la-paz`; hay que excluir la nueva si no es de Corrientes).
3. **Script idempotente** `wordpress/scripts/<ciudad>-AAAA-MM-DD.php`: crea la guía en es (y pt/en cuando corresponda) con `$aplicar` (simulacro) y lectura de verificación; no toca lo que ya existe. Lo dudoso va como nota `PENDIENTE:` (no se ve en la web). Un prestador, a lo sumo, como "listado con información pública" (`docs/prestadores.md`).
4. **Coordenadas y mapa del inicio**: la ciudad entra al mapa del hero con lat/lon; verificar que cae en su lugar (`docs/wordpress.md` → "Mapa del inicio"). Las etiquetas cercanas se separan solas.
5. **Vigilancia**: sumar sus fuentes a `vigilancia/fuentes.json` (`docs/vigilancia.md` §3). No sumar URLs adivinadas.
6. **Verificación en vivo**: agregar la guía a `verify-live.yml` (200, TouristDestination, sin noindex ni PENDIENTE, un dato propio).
7. **Flujo**: rama → PR → `test-wp.yml` en verde → merge a `main` (deploy automático) → `wp-script.yml` con `script=<nombre>` primero en simulacro, leer el log, después en vivo → `verify-live.yml` → pedir captura al dueño.

## Reglas que ya se aplicaron (no relitigar)

- Licencia: no publicamos aranceles sin fuente oficial vigente. Si no se puede confirmar online: "no lo podemos confirmar; preguntale a tu guía o a Recursos Naturales" (frase aprobada: *"si no lo ves online, preguntale a tu guía"*).
- Vedas: mínimo esfuerzo; párrafo genérico y enlace a la autoridad, sin fechas ni km.
- Fiestas: una ciudad suele tener varias; no sobre-indexar en una ("destaca su fiesta…"). Precio de una edición pasada, con año.
- Tallas, cupos y aranceles de revistas o prensa no son norma: no se publican como hecho.

## Pendientes de ciudades

- Próximas (matriz SEO, `docs/seo-oportunidades.md`): San Martín de los Andes / Junín de los Andes (mosca) → Río Grande → lagunas (Junín/Laguna de Gómez, Chascomús, Lobos).
- La Paz: tallas, cupos y vedas vigentes de Entre Ríos sin confirmar (ronda 2 de Gemini en `docs/investigacion/la-paz.md` §4). Esquina sin traducir a pt/en.
