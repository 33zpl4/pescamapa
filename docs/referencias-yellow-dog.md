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

## Decidido (dueño, 27/10/2026): no es un punto de ataque
El informe critica que Yellow Dog **oculta el contacto del prestador**. Nuestro modelo hace lo mismo y es intencional: el negocio necesita una comisión (fase 2: cobrarle a los guías por figurar o vender paquetes nosotros), así que no se lista todo ni se publica el contacto. No usamos "directorio abierto" como diferencial ni criticamos a quien lo hace. Fase 1 = tráfico con información gratis e imparcial. Ver `docs/prestadores.md` ("Modelo de negocio y qué se muestra").
