# Cómo editar Pescamapa (sin programar)

El sitio se edita desde **Pages CMS**, un editor web. Cada vez que guardás,
el sitio se actualiza solo en 2 o 3 minutos.

## Entrar

1. Abrí **https://app.pagescms.org**.
2. La primera vez, usá el link de invitación que te llegó por mail. No
   hace falta cuenta de GitHub ni contraseña: entrás con tu mail y te llega
   un enlace para ingresar.
3. Elegí el proyecto **pescamapa**, rama **main**.

A la izquierda vas a ver las secciones:

| Sección | Qué es |
|---|---|
| **Próximos destinos** | Las tarjetas "Próximamente" del inicio y los puntos grises del mapa |
| **Guías de destino — Español / Português / English** | Las páginas completas de cada lugar (hoy: Goya) |
| **Especies — Español / Português / English** | Surubí, dorado… |
| **Media** | Las fotos subidas |

## Tareas comunes

### Agregar una ciudad "Próximamente"

1. **Próximos destinos → Add entry**.
2. Completá el nombre, la provincia, la región, el río o la zona, y las especies.
3. **Latitud y longitud**: en Google Maps, hacé clic derecho sobre la ciudad
   y tocá los números que aparecen arriba (se copian). El primero es la
   latitud (ej. -27.32) y el segundo la longitud (ej. -58.57).
4. Foto y crédito (ver más abajo). **Save**.

Aparece en el inicio, en el mapa del hero y en la página del mapa, en los
tres idiomas. Si no ponés el nombre en portugués o en inglés, se usa el de
español.

### Cambiar la foto de una ciudad

1. Abrí la ciudad (en **Próximos destinos** o en **Guías de destino**).
2. En **Foto**, tocá la imagen, subí la nueva y elegila.
3. En **Crédito de la foto** escribí quién la sacó: `Foto: Nombre Apellido`.
4. **Save**.

Reglas de fotos:

- **Horizontal** y de al menos **1200 px** de ancho. El sitio la achica y
  la optimiza solo.
- **Solo fotos con permiso**: propias, de alguien que te la cedió, o con
  licencia libre. **Nunca bajes fotos de Google**: casi todas tienen
  derechos de autor.
- **Sin crédito, el sitio no se actualiza.** Es a propósito: siempre hay
  que nombrar al autor.

### Cambiar el titular, el subtítulo o el texto de una guía

En **Guías de destino → Español → Goya**:

- **Titular de la página**: el título grande de la ficha.
- **Subtítulo**: la bajada debajo del título. También aparece en la tarjeta del inicio.
- **Título y descripción para Google**: lo que se ve en los resultados de
  búsqueda. Tienen un límite de caracteres, y el editor avisa si te pasás.
- **Texto de la guía**: el cuerpo, con negritas, listas y links.

Si cambiás algo importante en español, cambialo también en **Português** y
**English** (misma ciudad, misma "Clave").

### Crear una guía completa nueva

1. **Guías de destino — Español → Add entry**.
2. **Clave**: el identificador, en minúsculas, sin tildes ni espacios (ej.
   `paso-de-la-patria`). Si ya existe un "Próximo destino" con ese mismo
   nombre, su tarjeta "Próximamente" se reemplaza sola por la guía.
3. **Dirección web**: el final de la URL (ej. `pesca-en-paso-de-la-patria`).
   **No la cambies después de publicar.**
4. Dejá el **Estado** en **Borrador** mientras la armás.

## Borrador o publicado: la regla de oro

- **Borrador**: la página se ve en el sitio con un cartel amarillo de "en
  construcción", pero Google no la indexa. Sirve para revisar.
- **Publicado**: solo funciona si se cumplen las tres condiciones:
  1. Hay al menos una **fuente oficial** (gobierno, municipio, organismo).
  2. Una persona **verificó el reglamento vigente** con la autoridad, y esa
     fecha está cargada en **Reglamento → Fecha en que una persona verificó…**
     (vale por un año).
  3. **No queda ninguna línea que empiece con `PENDIENTE:`**.

Si marcás "Publicado" y falta algo, **el sitio no se rompe**: queda online
la versión anterior y el cambio no aparece. En ese caso, revisá estas tres
condiciones.

Una traducción no puede publicarse antes que la versión en español.

## Si un cambio no aparece

1. Esperá 3 minutos y recargá la página con Ctrl+Shift+R.
2. Si sigue igual, lo más probable es que falte un dato obligatorio: una
   foto sin crédito, "Publicado" sin reglamento verificado o una descripción
   demasiado corta o larga. Revisá el último cambio que guardaste.
3. Si no lo encontrás, avisá: el error queda registrado y se ve exactamente
   qué campo falta.

## Lo que no se edita desde acá

El diseño, los textos fijos de la interfaz (botones, menú, footer) y la
lista de especies que aparece en los selectores. Para cambiar esas cosas
hace falta tocar el código.
