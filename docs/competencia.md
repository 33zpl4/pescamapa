# Competencia: primera lectura (29/10/2026)

Fuente: workflow *Investigar sitios* (`docs/investigar.md`), corrida `competencia-1`, rama `investigacion`. **Solo la página principal y el sitemap de
cada sitio**: es una lectura de estructura, no de contenido profundo. Los cuatro respondieron (ninguno bloqueó al robot). Nada de esto se copia;
sirve para decidir qué hacemos mejor.

## Resumen por sitio

| | Qué es | Tamaño (sitemap) | SEO visible | Monetización |
| --- | --- | --- | --- | --- |
| **Rumbo Pesca** (`okumafishingargentina.com`) | Plataforma de planificación: 109 destinos, 14 especies, 35 guías, mapa, técnicas, reglamentos por jurisdicción. Nació como catálogo de una marca de equipos (Okuma) y lo declara ("catálogo histórico", "salidas comerciales identificadas") | 303 URLs; `lastmod` en 74 (31/08 → 25/10/2026) | `Organization` + `WebSite` con buscador; sin hreflang; título y descripción cuidados; URLs limpias `/lugares-de-pesca/<provincia>/<lugar>` | Equipos (afiliación identificada); no vende salidas |
| **pescaargentina.com.ar** | Portal clásico de noticias y datos: dónde pescar (por región y provincia), especies, altura de ríos, fases lunares, mareas, reglamento y licencia, "excursiones de pesca" | 886 URLs, sin `lastmod` | `index, follow`, canonical; sin datos estructurados ni hreflang; mucho contenido de noticias (24 enlaces a noticias en la portada) | Publicidad / enlaces externos (22 en la portada) |
| **Pesca Pura** (`pescapura.com.ar`) | Buscador por provincia → ciudad → ficha (especies, guías por WhatsApp, tiendas, luna, clima, mapa); cursos, creadores de contenido, productos (Mercado Libre) | **1 URL** (todo se carga con JavaScript en una sola página) | Meta description larga (190 car.), sin datos estructurados, sin hreflang. Sus contadores salen en 0 en el HTML: lo que Google lee sin ejecutar JS es poco | Anuncios de negocios ("Sumar mi negocio"), afiliados de Mercado Libre; contacto directo por WhatsApp |
| **Pesca Maniacos** (`pescamaniacos.com.ar`) | App (Android/iOS) con guías, alojamiento, paradores, marketplace usado, puntos de pesca colaborativos, clima; cobra con Mercado Pago | Sin sitemap; sitio de aterrizaje de 300 palabras | Sin canonical, sin datos estructurados, sin og | Marketplace y reservas por la app (intermediario con pago) |

## Lo que hacen bien y conviene tener en cuenta

- **Rumbo Pesca es el más parecido a nosotros** y el más cuidado: promesa de "fuentes y fechas de revisión", "correcciones abiertas" y "afiliación
  identificada" a la vista, reglamentos por jurisdicción, y planificador. Es el que más compite por la posición "información confiable".
- **pescaargentina.com.ar tiene volumen** (886 URLs: 43 especies, una página por provincia, altura de ríos, luna) y años de historia. Es el rival en
  búsquedas genéricas ("pesca en [provincia]", "altura del río").
- **Pesca Pura entendió el camino de monetización** (contacto directo por WhatsApp, "Sumá tu negocio"), pero su arquitectura de una sola página
  limita su alcance en Google.
- **Pesca Maniacos apunta a lo que dijiste de Booking**: reserva y pago dentro de la plataforma. Es la referencia del modelo, no del contenido.

## Dónde podemos hacerlo mejor (hipótesis a validar)

1. **Idiomas.** Ninguno de los cuatro tiene hreflang ni versión en portugués o inglés. Es el hueco más claro: pescador brasileño en Corrientes
   (Paso de la Patria, Goya, Esquina).
2. **Datos propios del río.** Nadie muestra el nivel de cada punto con página propia, historial y qué significa para pescar. Rumbo y pescaargentina
   enlazan a fuentes externas (INA) o a una página general.
3. **Una página de paquetes por destino con "qué incluye"** y camino de consulta. Pesca Pura y Pesca Maniacos conectan con guías, pero sin
   explicar qué incluye cada opción; Rumbo Pesca no vende.
4. **Fuentes y fecha de verificación visibles en cada dato**, que Rumbo ya promete: ahí tenemos que ser claramente mejores (fecha por dato, "no lo
   podemos confirmar" cuando corresponde), no solo igualar.
5. **Datos estructurados** (`TouristDestination`, `FAQPage`, `ItemList`/`TouristTrip`): solo Rumbo tiene lo básico.

## Qué no sabemos todavía

- Qué tan buenas son las páginas internas (destino, especie, reglamento): esta lectura fue de portadas.
- Tráfico, posiciones y antigüedad de cada sitio: no hay datos acá. Search Console propio y una herramienta de palabras clave lo darían.
- Qué cobran (si cobran) a prestadores y a anunciantes.

## Próxima corrida sugerida (profundidad)

Páginas internas que cubren lo nuestro, para comparar contra Paso de la Patria, Chascomús y el dorado:
- `https://okumafishingargentina.com/lugares-de-pesca/corrientes/paso-de-la-patria`
- `https://okumafishingargentina.com/lugares-de-pesca/buenos-aires/laguna-chascomus`
- `https://okumafishingargentina.com/especies/dorado`
- `https://okumafishingargentina.com/reglamentos`
- `https://www.pescaargentina.com.ar/pesca-deportiva/corrientes`
- `https://www.pescaargentina.com.ar/contenidos/altura-rios`

Con `navegador: true` solo para Pesca Pura si queremos ver su contenido real (carga por JavaScript); hay que decidirlo, porque abre cada página con un
navegador completo.
