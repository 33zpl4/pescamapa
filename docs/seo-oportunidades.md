# Oportunidades SEO — prioridades de contenido

> **Superado el 31/10/2026** por `docs/estrategia-seo-2026-10-31.md` (estrategia v2) y el backlog con puntaje en `docs/ideas-contenido-seo-2026-10-31.md`. Se conserva como historia de la decisión del 20/10.

Análisis del dueño (20/10/2026). Decide **qué destino se arma después** y
qué páginas sumamos. Objetivo de todo: ser útil para quien quiere pescar y
que Google y las IA nos citen.

## Matriz de prioridad

| Prioridad | Cluster | Por qué |
| --- | --- | --- |
| Alta | **Paso de la Patria, Esquina, Goya, La Paz** (Litoral) | Marca de destino, torneos, búsqueda todo el año, operadores activos |
| Alta | **San Martín / Junín de los Andes + mosca** (Chimehuín, Malleo, Lácar, Lolog) | Intención premium, alto valor, ríos específicos |
| Alta | **Río Grande (Tierra del Fuego)** | Destino trofeo mundial; logística, permisos y acceso privado |
| Media-alta | **Junín (Laguna de Gómez), Chascomús, Lobos** | Volumen alto de escapadas de pejerrey; requiere datos de acceso al día |
| Media | **Reconquista / Jaaukanigás** | Mercado menos saturado: humedal + Paraná |
| Media | **Costa atlántica y embalses** | Fragmentado: solo localidades con buenos datos de prestadores |

**Orden de guías:** Goya ✅ → Esquina → Paso de la Patria → La Paz →
San Martín y Junín de los Andes (guía combinada, 24/10/2026, en rama; modelo 2 de los wireframes) → Río Grande → lagunas.

## Consultas objetivo (long tail, no genéricas)

- Destino × especie: `pesca en Esquina`, `pesca de dorado en Corrientes`,
  `surubí en Goya`, `pesca con mosca San Martín de los Andes`,
  `trucha en Chimehuín`, `pejerrey Laguna de Gómez`.
- Reglamento: `licencia de pesca Argentina`, `reglamento pesca Patagonia
  2026 2027`, `pesca con devolución Corrientes` → resúmenes claros, costos,
  enlace oficial y **fecha de verificación visible**.
- Comercial: `guía de pesca Esquina`, `excursión de pesca San Martín de los
  Andes`, `lodge de pesca Patagonia` → qué incluye una salida (duración,
  equipo, lancha, rango de precios) + **consulta que llega a Pescamapa**.

## Arquitectura

- Jerarquía: Argentina → región → destino → agua (río/laguna) → especie /
  técnica. Enlaces en las dos direcciones (especie ↔ destino ↔ reglamento ↔
  prestadores).
- **Eventos con URL estable** (`/eventos/fiesta-nacional-del-surubi/`), no
  una URL por año: se actualizan fechas y programa en la misma página.
- Entrevistas a guías locales: contenido propio con nombre y fecha, citas
  dentro de la guía del destino (`/entrevistas/`).
