# Decisiones

## 19/10/2026 — Astro + Cloudflare, no WordPress

El corazón del sitio son datos (destino × especie × temporada × reglamento ×
prestador) que generan cientos de páginas y un mapa. En WordPress eso
requería varios plugins (tipos de contenido y campos, multilenguaje, mapa,
directorio, caché), con la base de datos pisando a los archivos. En Astro
los datos viven en git, el esquema los valida en cada build, el hosting es
gratis y estático (Core Web Vitals) y todo se opera por git.

Cuándo se suma servidor: cuando los prestadores carguen sus fichas, paguen
destacados o haya cuentas (Cloudflare Workers + D1, o Supabase). Un editor
web para redactores no técnicos se puede sumar sobre el mismo repo
(Keystatic / Decap) sin migrar.

## 19/10/2026 — Goya como primer destino (en lugar de Esquina)

Pedido del dueño. Ficha armada con "Guia_pesca_Goya_Pescamapa" (fuentes
oficiales, consultadas el 19/10/2026). Queda en `borrador` hasta verificar
el reglamento vigente con Recursos Naturales de Corrientes y completar los
PENDIENTE (calendario por especie armado con guías locales, directorio).

URL: `/litoral/corrientes/pesca-en-goya/`, según la arquitectura de la guía
de wireframes (región/provincia/destino). El informe de Goya sugería
`/argentina/corrientes/goya/pesca/` con páginas hijas (/guias/, /isoro/,
/fiesta-nacional-del-surubi-2027/): las hijas se pueden colgar debajo de la
URL actual cuando existan.

## Pendiente de decidir

- Mosaicos del mapa para producción (OSM no admite tráfico alto).
- Portugués: primeras páginas pt para el público brasileño del Litoral.
- Fotos: hoy las tarjetas usan la ilustración CSS del prototipo.
