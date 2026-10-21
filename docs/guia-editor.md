# Cómo editar Pescamapa (sin programar)

Para Cheyenne y quien edite. Se edita en **WordPress**, en español. Lo que
guardás se ve en la web al instante. Portugués e inglés los hacemos
nosotros: vos trabajás solo en español.

## Entrar

**https://pescamapa.com/wp-admin** → tu usuario y contraseña. Entrás
directo a la lista de **Destinos**.

Menú de la izquierda:

| Sección | Qué es |
| --- | --- |
| **Destinos** | Las guías de cada lugar (Goya, Esquina…) |
| **Especies** | Surubí, dorado… |
| **Próximos destinos** | Las tarjetas "Próximamente" del inicio y los puntos grises del mapa |
| **Entrevistas** | Charlas con guías y pescadores (ver `docs/entrevistas.md`) |
| **Prestadores** | Guías, alojamientos y operadores. El contacto es interno y nunca se publica |
| **Consultas** | Lo que la gente pide desde la web (cotizaciones, prestadores que se quieren sumar) |
| **Fotos** | La biblioteca de imágenes |

**Nada te bloquea al guardar.** Arriba de cada guía, el cuadro "Para
completar la ficha" sugiere qué falta. Es solo una guía y no se ve en la web.

## Tareas comunes

### Cambiar la foto de una guía
1. Destinos → la guía.
2. Columna derecha → **Imagen destacada** → clic en la foto → elegí otra o
   subí una nueva.
3. **Actualizar**.
4. En **Fotos** → esa foto, completá **Autor de la foto**. Es lo único que
   se pide; en la web sale "Foto: Nombre Apellido".

Conviene que sea **horizontal**: una vertical se recorta mucho en la portada.

### Cambiar un título o un texto
Destinos → la guía. El **titular**, el **subtítulo** y los demás datos están
en los recuadros de arriba, por pestañas (Textos, Aguas y calendario,
Reglamento, Preguntas y fuentes). El texto largo está en el editor de abajo.
**Actualizar**.

### Notas internas ("PENDIENTE")
Si escribís un párrafo que empieza con `PENDIENTE:`, no se ve en la web.
Sirve para dejar anotado qué falta confirmar.

### Cargar o completar un prestador
Prestadores → el prestador (o "Añadir nuevo"):
- **Lancha, motor, capacidad**, equipo y qué incluye: es lo primero que
  mira un pescador.
- **Precio**, con su **fecha**. Sin fecha, el precio no se muestra.
- **Contacto** (pestaña interna): nunca se publica, las consultas llegan a
  nosotros.
- Para que aparezca en la web, tildá **"Aceptó aparecer"** y **destildá
  "Datos de ejemplo"**.

Mientras estés logueada, en la guía ves también los que todavía no son
públicos, como tarjetas punteadas de "Vista previa".

### Atender una consulta
Consultas → la consulta. Ahí ves lo que escribió la persona, con su
WhatsApp clickeable. A la derecha cambiás el **Estado** (respondida,
derivada a un prestador, concretada…), elegís **a quién se derivó** y dejás
notas. El menú muestra cuántas consultas nuevas hay.

### Una entrevista
Entrevistas → Añadir. La guía completa (preguntas y cómo cargarla) está en
`docs/entrevistas.md`. Sin la marca **"Aceptó que publiquemos…"** no se
publica nada.

### Revisión de fotos
El 1.º de cada mes llega a GitHub un issue "Revisión de fotos" con lo que
conviene mirar: portadas verticales o chicas, fotos sin autor, nombres feos
y fotos sin usar. Nada es urgente ni bloquea.

## Si algo no anda
Mandá una captura del mensaje exacto. Si necesitás hacer algo que el panel
no te deja, pedilo y lo habilitamos.
