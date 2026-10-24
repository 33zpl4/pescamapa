# Dossier: San Martín y Junín de los Andes (Neuquén)

Fecha: 2026-10-24. Interno, no se publica. Quinta guía (siguiente de la matriz SEO después de La Paz, `docs/seo-oportunidades.md`). Estructura de contenido: `docs/seo-guia-wireframes.md` (modelo 2, guía combinada).

> **Cómo se hizo esta vez.** No hubo informe de Gemini/ChatGPT: Claude buscó con la herramienta de búsqueda del sandbox y **no pudo abrir ninguna página** (`getaddrinfo ENOTFOUND` en `cazaypesca.neuquen.gob.ar`, `turismo.neuquen.gob.ar` y `noticiasnqn.com.ar`). Lo de abajo sale de **resúmenes de buscador**, no de páginas abiertas. Por la regla de `docs/investigacion/README.md` ("un enlace que nadie abrió no es fuente") solo se publicó lo que **dos medios distintos** dicen igual y lo que la estructura de la guía necesita. Todo lo demás quedó como "no lo podemos confirmar" y nota `PENDIENTE:` oculta.

## 1. Jurisdicción (lo primero que se verifica)

**Neuquén, Patagonia.** Autoridad: Dirección Provincial de Fauna (Caza y Pesca de Neuquén); dentro del Parque Nacional Lanín, también Parques Nacionales. Reglas: Reglamento de Pesca Continental Patagónico (unificado entre provincias patagónicas) más disposiciones por ambiente. **No aplica nada de Corrientes ni de Entre Ríos**: el script `licencia-contacto-2026-10-22` excluye la clave `san-martin-junin`.

## 2. Qué se publicó (y con qué respaldo)

| Dato publicado | Respaldo | Estado |
| --- | --- | --- |
| Venta online de permisos 2026-2027 desde el 15/10/2026, en `cazaypesca.neuquen.gob.ar/permisos` | Noticias NQN (15/10/2026) y ANBariloche (16/10/2026), vía buscador | Dos medios coinciden; página oficial **sin abrir** |
| Temporada en la Patagonia empieza el 1 de noviembre; en algunos ambientes cierra el 1 de mayo de 2027 | Las mismas notas | Se publica con "según la prensa" y "cada ambiente tiene su fecha" |
| Reglamento de Pesca Continental Patagónico compartido entre provincias | Las mismas notas | Idem |
| El sitio de permisos trae un asistente (chatbot) con las disposiciones por ambiente | Las mismas notas | Idem |
| Ambientes de la zona: Chimehuín, Malleo, Collón Curá, Correntoso, Lácar | Folleto de pesca 2026 de Neuquén Turismo (lista que devolvió el buscador) y `seo-guia-wireframes.md` | Solo se nombran, sin reglas |
| Aeropuerto de Chapelco (CPC); Junín a ~40 km por RN 234 | Conocimiento general, sin fuente abierta | Hay nota `PENDIENTE` para confirmar |

## 3. Qué **no** se publicó

- **Valores del permiso 2026-2027** (residentes, no residentes, extranjeros, diario, semanal, temporada): ninguna nota los trae. Regla del 22/10/2026: sin fuente oficial vigente no se publican aranceles. Se dice "no pudimos confirmarlo; mirálo en el sitio oficial o preguntale a tu guía".
- **Reglas por ambiente** (solo mosca en La Boca del Chimehuín, cupos, devolución obligatoria, cierres, horarios) y **qué rige dentro del Lanín** (incluido si hay que pagar ingreso al parque): sin fuente abierta.
- **Limpieza de equipos por didymo**: está en el wireframe, sin fuente abierta.
- **Fecha del cierre y excepciones por ambiente**, y la nota de 2021 sobre apertura anticipada del Huechulafquen (1 de octubre): vieja, no se usa.
- **Prestadores**: ninguno cargado (no hay fuente abierta ni consentimiento). Se invita a guías y lodges a escribir a info@pescamapa.com.
- **Especie**: la guía no marca especies porque no existe todavía la ficha de trucha (solo dorado y surubí); ver pendiente en `docs/estado.md`.

## 4. Pendientes: ronda 1 para Gemini/ChatGPT (el dueño pega y devuelve)

Pegar cada una por separado; pedir enlace abierto y fecha de la fuente; cerrar con "si no lo encontrás, decí NO CONFIRMADO; no estimes".

1. *¿Cuáles son los valores del permiso de pesca deportiva de Neuquén para la temporada 2026-2027, por categoría (residente, argentino no residente, extranjero; diario, semanal, temporada)? Dame la disposición y el enlace oficial donde se publican.*
2. *¿Cuál es el Reglamento General de Pesca Continental Patagónico vigente para 2026-2027 y dónde está el texto (URL)? ¿Qué disposiciones específicas hay para los ríos Chimehuín (La Boca, arriba, abajo) y Malleo?*
3. *¿Qué normas de pesca rigen dentro del Parque Nacional Lanín (lago Lácar, lagos Huechulafquen, Paimún, Epulafquen) además de las provinciales? ¿Se cobra ingreso al parque o un permiso aparte? Dame la fuente de Parques Nacionales.*
4. *¿Qué exigen las autoridades de Neuquén sobre limpieza y desinfección de equipos de pesca (didymo)? Dame la norma o el aviso oficial.*
5. *¿Cuáles son las fechas de cierre de la temporada 2026-2027 por ambiente (y excepciones de apertura) en la zona San Martín–Junín–Aluminé? Dame el calendario oficial.*
6. *¿Qué guías de pesca con mosca y lodges de San Martín y Junín de los Andes tienen sitio propio activo, con contacto y tarifa de jornada publicada hoy?* (Solo para ubicar candidatos: se confirman en su sitio, `docs/prestadores.md`.)

Al volver, auditar con la checklist de `docs/investigacion/README.md` y completar el script `san-martin-junin-2026-10-24` (o escribir uno nuevo `san-martin-junin-<fecha>.php`, como `la-paz-precio-2026-10-22`).

## 5. Vigilancia

Se cargaron tres fuentes "solo responde" en `vigilancia/fuentes.json` (`nqn-permisos`, `nqn-folleto-pesca`, `nqn-prensa-permisos`) para que el informe semanal diga si abren desde GitHub. Pasar a `texto` cuando se confirmen.

## 6. Mapa y foto

Pin = el de la tarjeta "próximamente" (lat -39.94, lon -71.07, Junín de los Andes); la guía hereda su foto (Commons, "SAN MARTIN DE LOS ANDES LACAR.JPG", crédito en `src/data/fotos.json`). La tarjeta se oculta sola porque la guía usa la misma `clave` (`san-martin-junin`). Verificar en vivo que el pin cae en su lugar y que la etiqueta no pisa a otra.
