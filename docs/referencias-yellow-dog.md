# Referencia: Yellow Dog Flyfishing (revisión del 27/10/2026)

Informe recibido por el dueño sobre la web de una agencia de viajes de pesca con mosca de EE. UU. Es una referencia de **estructura y UX**; sus cifras de negocio (comisión de 15 a 20 %) **no están verificadas** y no se usan.

## Qué se adopta (hecho)
| Idea | Qué hicimos | Dónde |
| --- | --- | --- |
| Datos clave arriba, legibles en 3 segundos | La caja "Resumen" de cada guía suma **Licencia y reglas** (autoridad, enlaza a la sección) y **Hoy** (altura del río o viento de los próximos días, enlaza al bloque "Cómo está el río") | `vistas/destino.php` |
| Navegación por secciones, no un testamento de texto | Barra **"En esta guía"** con atajos a: hoy en el agua, calendario, licencia y reglas, guías y alojamiento, preguntas y fuentes (solo las que existen) | `vistas/destino.php`, `assets/extra.css` |
| Informes de río en tiempo real | Ya existe: panel del río (Litoral y Buenos Aires), una página por punto, clima por guía | `docs/rio.md` |
| Transparencia: decir lo que no sabemos | Ya es regla: "no lo podemos confirmar; preguntale a tu guía" | `docs/investigacion/README.md` |

## Qué se deja para después (necesita contenido que no inventamos)
- **"Para quién es y para quién no"** por destino: es juicio editorial con fuente (ej. horas al sol, chicos, mareo); agregar un campo `para_quien` cuando haya dato de guías o notas.
- **Íconos rápidos** (con devolución, bajada municipal, apto lancha propia, apto familias): dependen de datos de accesos (`docs/rampas.md`).
- **Conectividad y estado de caminos** (señal, ripio con lluvia): dato por destino, hoy sin fuente.
- **Checklist de equipo y afiliados**: decisión comercial del dueño; ojo con la regla de no nombrar marcas en superficies de producto.
- **Videos incrustados** y **podcast**: contenido propio; las entrevistas ya existen (`/entrevistas/`).

## Para decidir con el dueño
El informe critica que esa agencia **oculta el contacto del prestador** y obliga a pasar por su formulario. Nuestro modelo hace lo mismo hoy (el contacto del prestador es interno, las consultas llegan a Pescamapa: `docs/prestadores.md`). Es válido para cobrar una comisión, pero el posicionamiento "directorio abierto y verificado" convive mal con esconder el contacto. Opciones: mantener el modelo (consulta intermediada) y decirlo con claridad, o publicar el contacto de quien lo consienta y cobrar por otra vía.
