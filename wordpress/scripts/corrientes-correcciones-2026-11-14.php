<?php
/**
 * Correcciones de Corrientes, 14/11/2026 (edición de contenido ya publicado, es / en / pt; slugs y títulos no cambian).
 * Fuente de verdad de las vedas: verificación del 14/11/2026 (carpetas vedas-1/2/3 de la rama `investigacion`: avisos del Ministerio de Turismo de
 * Corrientes, El Litoral, Perfil, Corrientes al Día, Elonce, Impacto Corrientes); reglas del Iberá: corr-fix y corr-fix2 de la misma rama (página del
 * Ministerio de Turismo, página de legislación del Parque Iberá de 2015, página de El Tránsito).
 *  1. Guías itati, ita-ibate y esteros-del-ibera: la sección «Vedas: lo que pasó en 2026» se reescribe con la cronología atribuida (618/26 del 29/5;
 *     639/26 la levantó a las 12 del 3/6; 9/9 18:00 km 1109 a 1150; tramos de septiembre según El Litoral y Perfil; 1054/26 la levantó a las 13 del
 *     29/9; 1249/26 del 13/11 según Impacto Corrientes) y el cierre «se dictan y se levantan de un día para otro». Itatí (km 1279) e Itá Ibaté (km 1375)
 *     quedan aguas arriba de todos los tramos informados, dicho atribuido y sin prometer nada. Fuera «no leímos las disposiciones», «número pendiente»
 *     y todo lo que decía que la veda del 9/9 seguía vigente. También: reglamento_resumen, la pregunta de las vedas (Itatí e Itá Ibaté), las fuentes,
 *     las notas internas ocultas (PENDIENTE) que decían qué no pudimos abrir.
 *  2. Esteros del Iberá, reglas: dos lugares con pesca deportiva dentro de la reserva (Ministerio de Turismo), pesca extractiva prohibida y deportiva con
 *     devolución en áreas protegidas (Disposiciones 09/10 y 02/11 de la Dirección de Parques y Reservas) y vedas por especie en el Decreto 1304/78
 *     (página de legislación del Parque Iberá, 2015), y lo que dice El Tránsito en su página. No se afirma que valgan para campos o lodges privados.
 *  3. Pines: Itá Ibaté y Esteros del Iberá con la coordenada de Wikipedia (es), leída el 14/11/2026. Itatí queda como está: la página de Wikipedia
 *     que se leyó no trae coordenada.
 *  4. Notas: tallas-cupos-vedas, golden-dorado-guia, cuando-pescar-dorado, cuando-pescar-surubi (el ítem de la veda de surubí y/o el del dorado) y las
 *     dos notas de precios (viaje-dorado-amigos-precios: tabla de vedas; precio-viaje-dorado-lodge: la licencia de Itatí es según agencias). El mismo
 *     texto está en wordpress/contenido/precios/paginas.{es,en,pt}.json.
 * Solo se reemplaza lo que se reconoce; lo que no se encuentra se avisa y no se toca. Idempotente. Simulacro primero ($aplicar = false).
 * Se corre con wp-script.yml (script = corrientes-correcciones-2026-11-14).
 */
$aplicar = true;
$hoy     = '20261114';

$D = json_decode( <<<'JSON'
{
 "guias": {
  "itati": {
   "n": "Itatí",
   "km": "1279"
  },
  "ita-ibate": {
   "n": "Itá Ibaté",
   "km": "1375"
  },
  "esteros-del-ibera": {
   "n": null,
   "km": null
  }
 },
 "sec": {
  "es": [
   "Vedas: lo que pasó en 2026"
  ],
  "en": [
   "Closed seasons: what happened in 2026"
  ],
  "pt": [
   "Defesos: o que aconteceu em 2026"
  ]
 },
 "intro": {
  "es": "Recursos Naturales de Corrientes puede disponer vedas extraordinarias por tramo del Paraná, y en 2026 lo hizo varias veces. Esta es la cronología y quién la informó:",
  "en": "Recursos Naturales of Corrientes can order extraordinary closures on stretches of the Paraná, and in 2026 it did so several times. This is the timeline and who reported it:",
  "pt": "Recursos Naturales de Corrientes pode decretar defesos extraordinários por trecho do Paraná, e em 2026 o fez várias vezes. Esta é a cronologia e quem a informou:"
 },
 "items": {
  "es": [
   "<strong>29 de mayo de 2026.</strong> La Disposición 618/26 prohibió toda pesca (deportiva, comercial y de subsistencia) entre los km 1232 y 1238, de la confluencia del Paraná con el Paraguay a Los Cables, por una concentración de surubíes (según <a href=\"https://www.ellitoral.com.ar/sociedad/2026-5-29-16-52-0-corrientes-establece-veda-extraordinaria-de-pesca-en-el-rio-parana-por-concentracion-de-surubies\" rel=\"noopener\">El Litoral</a> y el <a href=\"https://corrientes.tur.ar/la-pesca-deportiva-retoma-su-actividad-plena-en-corrientes-tras-el-levantamiento-de-la-veda-extraordinaria/\" rel=\"noopener\">aviso del Ministerio de Turismo de Corrientes</a>).",
   "<strong>3 de junio de 2026, 12:00.</strong> La Disposición 639/26 levantó esa veda y volvieron a regir las modalidades de la Disposición 853/22 (según el <a href=\"https://corrientes.tur.ar/la-pesca-deportiva-retoma-su-actividad-plena-en-corrientes-tras-el-levantamiento-de-la-veda-extraordinaria/\" rel=\"noopener\">aviso del Ministerio de Turismo de Corrientes</a>).",
   "<strong>9 de septiembre de 2026, 18:00.</strong> Veda extraordinaria entre los km 1109 y 1150 (ambos inclusive) por una concentración de dorados: toda pesca prohibida, con multa de 500 litros de nafta súper e inhabilitación mínima de seis meses, y la prohibición seguía al cardumen si se desplazaba (según el <a href=\"https://corrientes.tur.ar/veda-extraordinaria-en-el-rio-parana-por-concentracion-de-dorados/\" rel=\"noopener\">aviso del Ministerio de Turismo de Corrientes</a>). Es la Disposición 931, según La Tribuna TV (11/9/2026).",
   "<strong>Septiembre de 2026.</strong> Según <a href=\"https://www.ellitoral.com.ar/sociedad/2026-9-14-7-43-0-corrientes-extienden-la-veda-extraordinaria-en-el-rio-parana\" rel=\"noopener\">El Litoral</a> (14/9), la veda alcanzó el tramo entre los km 1140 (Puerto Empedrado) y 1208 (Punta San Sebastián, en la Capital); según <a href=\"https://www.perfil.com/noticias/nea/fijaron-la-veda-extraordinaria-en-un-nuevo-tramo-del-rio-parana-para-proteger-dorados-y-sabalos.phtml\" rel=\"noopener\">Perfil</a> (16/9), otro entre los km 1180 y 1239, del riacho INTA a la zona de Confluencia, en jurisdicción de Paso de la Patria.",
   "<strong>29 de septiembre de 2026, 13:00.</strong> La Disposición 1054/26 levantó la veda y volvió a regir la Disposición 853/22 (según <a href=\"https://corrientesaldia.info/2026/09/29/corrientes-levanto-la-veda-extraordinaria-de-pesca-en-el-rio-parana/\" rel=\"noopener\">Corrientes al Día</a>, <a href=\"https://www.elonce.com/parana/levantaron-la-veda-extraordinaria-de-pesca-en-el-rio-parana-tras-la-dispersion-de-surubies-y-dorados.htm\" rel=\"noopener\">Elonce</a> y <a href=\"https://www.perfil.com/noticias/nea/corrientes-levanto-la-veda-extraordinaria-de-pesca-en-el-rio-parana-que-cambia-desde-hoy.phtml\" rel=\"noopener\">Perfil</a>).",
   "<strong>13 de noviembre de 2026, 12:00.</strong> Según <a href=\"https://www.impactocorrientes.com/notix/noticia/246698/index.html\" rel=\"noopener\">Impacto Corrientes</a> (13/11/2026), la Disposición 1249/26 prohibió toda pesca frente a la Costanera de la ciudad de Corrientes, entre Punta San Sebastián y Punta Tacurú, por un cardumen de manduré cuchara; alcanza a todas las especies."
  ],
  "en": [
   "<strong>29 May 2026.</strong> Disposición 618/26 banned all fishing (sport, commercial and subsistence) between km 1232 and 1238, from the Paraná–Paraguay confluence to Los Cables, because of a concentration of surubí (according to <a href=\"https://www.ellitoral.com.ar/sociedad/2026-5-29-16-52-0-corrientes-establece-veda-extraordinaria-de-pesca-en-el-rio-parana-por-concentracion-de-surubies\" rel=\"noopener\">El Litoral</a> and the <a href=\"https://corrientes.tur.ar/la-pesca-deportiva-retoma-su-actividad-plena-en-corrientes-tras-el-levantamiento-de-la-veda-extraordinaria/\" rel=\"noopener\">notice from the Corrientes Ministry of Tourism</a>, both in Spanish).",
   "<strong>3 June 2026, 12:00.</strong> Disposición 639/26 lifted that closure and the rules of Disposición 853/22 applied again (according to the <a href=\"https://corrientes.tur.ar/la-pesca-deportiva-retoma-su-actividad-plena-en-corrientes-tras-el-levantamiento-de-la-veda-extraordinaria/\" rel=\"noopener\">Corrientes Ministry of Tourism notice</a>).",
   "<strong>9 September 2026, 18:00.</strong> Extraordinary closure between km 1109 and 1150 (both inclusive) because of a concentration of dorado: all fishing banned, with a fine of 500 litres of super petrol and a minimum six-month disqualification, and the ban followed the shoal if it moved (according to the <a href=\"https://corrientes.tur.ar/veda-extraordinaria-en-el-rio-parana-por-concentracion-de-dorados/\" rel=\"noopener\">Corrientes Ministry of Tourism notice</a>). It is Disposición 931, according to La Tribuna TV (11/9/2026).",
   "<strong>September 2026.</strong> According to <a href=\"https://www.ellitoral.com.ar/sociedad/2026-9-14-7-43-0-corrientes-extienden-la-veda-extraordinaria-en-el-rio-parana\" rel=\"noopener\">El Litoral</a> (14/9), the closure reached the stretch between km 1140 (Puerto Empedrado) and 1208 (Punta San Sebastián, in the capital); according to <a href=\"https://www.perfil.com/noticias/nea/fijaron-la-veda-extraordinaria-en-un-nuevo-tramo-del-rio-parana-para-proteger-dorados-y-sabalos.phtml\" rel=\"noopener\">Perfil</a> (16/9), another between km 1180 and 1239, from the INTA creek to the Confluencia area, under Paso de la Patria jurisdiction.",
   "<strong>29 September 2026, 13:00.</strong> Disposición 1054/26 lifted the closure and Disposición 853/22 applied again (according to <a href=\"https://corrientesaldia.info/2026/09/29/corrientes-levanto-la-veda-extraordinaria-de-pesca-en-el-rio-parana/\" rel=\"noopener\">Corrientes al Día</a>, <a href=\"https://www.elonce.com/parana/levantaron-la-veda-extraordinaria-de-pesca-en-el-rio-parana-tras-la-dispersion-de-surubies-y-dorados.htm\" rel=\"noopener\">Elonce</a> and <a href=\"https://www.perfil.com/noticias/nea/corrientes-levanto-la-veda-extraordinaria-de-pesca-en-el-rio-parana-que-cambia-desde-hoy.phtml\" rel=\"noopener\">Perfil</a>).",
   "<strong>13 November 2026, 12:00.</strong> According to <a href=\"https://www.impactocorrientes.com/notix/noticia/246698/index.html\" rel=\"noopener\">Impacto Corrientes</a> (13/11/2026), Disposición 1249/26 banned all fishing off the Costanera of Corrientes city, between Punta San Sebastián and Punta Tacurú, because of a shoal of manduré cuchara; it covers all species."
  ],
  "pt": [
   "<strong>29 de maio de 2026.</strong> A Disposição 618/26 proibiu toda a pesca (esportiva, comercial e de subsistência) entre os km 1232 e 1238, da confluência do Paraná com o Paraguai até Los Cables, por uma concentração de surubins (segundo <a href=\"https://www.ellitoral.com.ar/sociedad/2026-5-29-16-52-0-corrientes-establece-veda-extraordinaria-de-pesca-en-el-rio-parana-por-concentracion-de-surubies\" rel=\"noopener\">El Litoral</a> e o <a href=\"https://corrientes.tur.ar/la-pesca-deportiva-retoma-su-actividad-plena-en-corrientes-tras-el-levantamiento-de-la-veda-extraordinaria/\" rel=\"noopener\">aviso do Ministério do Turismo de Corrientes</a>, em espanhol).",
   "<strong>3 de junho de 2026, 12:00.</strong> A Disposição 639/26 suspendeu esse defeso e voltaram a valer as modalidades da Disposição 853/22 (segundo o <a href=\"https://corrientes.tur.ar/la-pesca-deportiva-retoma-su-actividad-plena-en-corrientes-tras-el-levantamiento-de-la-veda-extraordinaria/\" rel=\"noopener\">aviso do Ministério do Turismo de Corrientes</a>).",
   "<strong>9 de setembro de 2026, 18:00.</strong> Defeso extraordinário entre os km 1109 e 1150 (ambos inclusive) por uma concentração de dourados: toda a pesca proibida, com multa de 500 litros de gasolina super e inabilitação mínima de seis meses, e a proibição acompanhava o cardume se ele se deslocasse (segundo o <a href=\"https://corrientes.tur.ar/veda-extraordinaria-en-el-rio-parana-por-concentracion-de-dorados/\" rel=\"noopener\">aviso do Ministério do Turismo de Corrientes</a>). É a Disposição 931, segundo La Tribuna TV (11/9/2026).",
   "<strong>Setembro de 2026.</strong> Segundo <a href=\"https://www.ellitoral.com.ar/sociedad/2026-9-14-7-43-0-corrientes-extienden-la-veda-extraordinaria-en-el-rio-parana\" rel=\"noopener\">El Litoral</a> (14/9), o defeso alcançou o trecho entre os km 1140 (Puerto Empedrado) e 1208 (Punta San Sebastián, na capital); segundo <a href=\"https://www.perfil.com/noticias/nea/fijaron-la-veda-extraordinaria-en-un-nuevo-tramo-del-rio-parana-para-proteger-dorados-y-sabalos.phtml\" rel=\"noopener\">Perfil</a> (16/9), outro entre os km 1180 e 1239, do riacho INTA até a zona de Confluencia, na jurisdição de Paso de la Patria.",
   "<strong>29 de setembro de 2026, 13:00.</strong> A Disposição 1054/26 suspendeu o defeso e voltou a valer a Disposição 853/22 (segundo <a href=\"https://corrientesaldia.info/2026/09/29/corrientes-levanto-la-veda-extraordinaria-de-pesca-en-el-rio-parana/\" rel=\"noopener\">Corrientes al Día</a>, <a href=\"https://www.elonce.com/parana/levantaron-la-veda-extraordinaria-de-pesca-en-el-rio-parana-tras-la-dispersion-de-surubies-y-dorados.htm\" rel=\"noopener\">Elonce</a> e <a href=\"https://www.perfil.com/noticias/nea/corrientes-levanto-la-veda-extraordinaria-de-pesca-en-el-rio-parana-que-cambia-desde-hoy.phtml\" rel=\"noopener\">Perfil</a>).",
   "<strong>13 de novembro de 2026, 12:00.</strong> Segundo <a href=\"https://www.impactocorrientes.com/notix/noticia/246698/index.html\" rel=\"noopener\">Impacto Corrientes</a> (13/11/2026), a Disposição 1249/26 proibiu toda a pesca em frente à Costanera da cidade de Corrientes, entre Punta San Sebastián e Punta Tacurú, por um cardume de manduré cuchara; vale para todas as espécies."
  ]
 },
 "lugar": {
  "rio": {
   "es": "Según el informe COMIP–UNNE, %1$s está en el km %2$s, aguas arriba de todos los tramos que informaron el Ministerio de Turismo y la prensa (entre los km 1109 y 1239). Los avisos del Ministerio traen una cláusula móvil: la prohibición sigue al cardumen si se desplaza.",
   "en": "According to the COMIP–UNNE report, %1$s is at km %2$s, upstream of every stretch reported by the Ministry of Tourism and the press (between km 1109 and 1239). The Ministry’s notices carry a moving clause: the ban follows the shoal if it moves.",
   "pt": "Segundo o relatório COMIP–UNNE, %1$s fica no km %2$s, rio acima de todos os trechos informados pelo Ministério do Turismo e pela imprensa (entre os km 1109 e 1239). Os avisos do Ministério trazem uma cláusula móvel: a proibição acompanha o cardume se ele se deslocar."
  },
  "esteros": {
   "es": "Las vedas extraordinarias de 2026 que informaron el Ministerio de Turismo y la prensa son del río Paraná; ninguna de esas notas menciona los Esteros del Iberá ni el río Corriente.",
   "en": "The 2026 extraordinary closures reported by the Ministry of Tourism and the press are on the Paraná river; none of those articles mentions the Esteros del Iberá or the Corriente river.",
   "pt": "Os defesos extraordinários de 2026 informados pelo Ministério do Turismo e pela imprensa são do rio Paraná; nenhuma dessas matérias menciona os Esteros do Iberá nem o rio Corriente."
  }
 },
 "cierre": {
  "es": "Las vedas extraordinarias se dictan y se levantan de un día para otro: antes de salir, consultá las vigentes con <a href=\"https://www.recnaturalesctes.gob.ar/\" rel=\"noopener\">Recursos Naturales</a> o con tu guía. En <a href=\"{n:tallas-cupos-vedas}\">la nota de tallas, cupos y vedas</a> está la misma cronología.",
  "en": "Extraordinary closures are ordered and lifted from one day to the next: before you go, check the current ones with <a href=\"https://www.recnaturalesctes.gob.ar/\" rel=\"noopener\">Recursos Naturales</a> or your guide. The same timeline is in <a href=\"{n:tallas-cupos-vedas}\">our note on size and bag limits and closed seasons</a>.",
  "pt": "Os defesos extraordinários são decretados e suspensos de um dia para o outro: antes de sair, consulte os vigentes com <a href=\"https://www.recnaturalesctes.gob.ar/\" rel=\"noopener\">Recursos Naturales</a> ou com o seu guia. A mesma cronologia está em <a href=\"{n:tallas-cupos-vedas}\">nossa nota de tamanhos, limites e defesos</a>."
 },
 "resumen_viejo": {
  "es": "La provincia puede disponer vedas extraordinarias por tramo del Paraná: en 2026 las hubo, según la prensa, y se levantaron el 29 de septiembre. Antes de salir, consultá las vigentes con Recursos Naturales.",
  "en": "The province can order extraordinary closures on stretches of the Paraná: in 2026 there were some, according to the press, and they were lifted on 29 September. Before you go, check the current ones with Natural Resources.",
  "pt": "A província pode decretar defesos extraordinários por trecho do Paraná: em 2026 houve, segundo a imprensa, e foram suspensos em 29 de setembro. Antes de sair, consulte os vigentes com Recursos Naturais."
 },
 "resumen_nuevo": {
  "es": "La provincia dicta vedas extraordinarias por tramo del Paraná y en 2026 lo hizo varias veces (la última, según la prensa, frente a la Costanera de la ciudad de Corrientes desde el 13 de noviembre). Se dictan y se levantan de un día para otro: antes de salir, consultá las vigentes con Recursos Naturales.",
  "en": "The province orders extraordinary closures on stretches of the Paraná and did so several times in 2026 (the latest, according to the press, off the Costanera of Corrientes city from 13 November). They are ordered and lifted from one day to the next: before you go, check the current ones with Natural Resources.",
  "pt": "A província decreta defesos extraordinários por trecho do Paraná e em 2026 o fez várias vezes (o último, segundo a imprensa, em frente à Costanera da cidade de Corrientes desde 13 de novembro). São decretados e suspensos de um dia para o outro: antes de sair, consulte os vigentes com Recursos Naturais."
 },
 "resumen_esteros": {
  "es": " En las áreas protegidas del Iberá, según la página de legislación del Parque Iberá (2015), la pesca extractiva está prohibida y la deportiva es con devolución (Disposiciones 09/10 y 02/11 de la Dirección de Parques y Reservas); las vedas por especie están en el Decreto 1304/78.",
  "en": " In the protected areas of the Iberá, according to the Iberá Park legislation page (2015), extractive fishing is banned and sport fishing is catch and release (Disposiciones 09/10 and 02/11 of the Dirección de Parques y Reservas); closed seasons by species are in Decreto 1304/78.",
  "pt": " Nas áreas protegidas do Iberá, segundo a página de legislação do Parque Iberá (2015), a pesca extrativa é proibida e a esportiva é com devolução (Disposições 09/10 e 02/11 da Dirección de Parques y Reservas); os defesos por espécie estão no Decreto 1304/78."
 },
 "faq_rx": "/veda|closure|defeso/iu",
 "faq": {
  "es": "Sí, en el Paraná. Según el Ministerio de Turismo de Corrientes y la prensa: del 29/5 al 3/6 (km 1232 a 1238, surubíes), del 9/9 al 29/9 (tramos entre los km 1109 y 1239, dorados) y, según Impacto Corrientes, desde el 13/11 frente a la Costanera de la ciudad de Corrientes. %1$s está en el km %2$s según el informe COMIP–UNNE, aguas arriba de todos esos tramos. Las vedas se dictan y se levantan de un día para otro: consultá las vigentes con Recursos Naturales antes de salir.",
  "en": "Yes, on the Paraná. According to the Corrientes Ministry of Tourism and the press: from 29/5 to 3/6 (km 1232 to 1238, surubí), from 9/9 to 29/9 (stretches between km 1109 and 1239, dorado) and, according to Impacto Corrientes, from 13/11 off the Costanera of Corrientes city. %1$s is at km %2$s according to the COMIP–UNNE report, upstream of all those stretches. Closures are ordered and lifted from one day to the next: check the current ones with Recursos Naturales before you go.",
  "pt": "Sim, no Paraná. Segundo o Ministério do Turismo de Corrientes e a imprensa: de 29/5 a 3/6 (km 1232 a 1238, surubins), de 9/9 a 29/9 (trechos entre os km 1109 e 1239, dourados) e, segundo Impacto Corrientes, desde 13/11 em frente à Costanera da cidade de Corrientes. %1$s fica no km %2$s segundo o relatório COMIP–UNNE, rio acima de todos esses trechos. Os defesos são decretados e suspensos de um dia para o outro: consulte os vigentes com Recursos Naturales antes de sair."
 },
 "fuentes_veda": {
  "es": [
   [
    "El Litoral: Corrientes establece veda extraordinaria de pesca en el río Paraná por concentración de surubíes (29/5/2026)",
    "https://www.ellitoral.com.ar/sociedad/2026-5-29-16-52-0-corrientes-establece-veda-extraordinaria-de-pesca-en-el-rio-parana-por-concentracion-de-surubies",
    0
   ],
   [
    "Ministerio de Turismo de Corrientes: aviso del levantamiento de la veda, Disposición 639/26 (3/6/2026)",
    "https://corrientes.tur.ar/la-pesca-deportiva-retoma-su-actividad-plena-en-corrientes-tras-el-levantamiento-de-la-veda-extraordinaria/",
    1
   ],
   [
    "Ministerio de Turismo de Corrientes: aviso de la veda extraordinaria por concentración de dorados (9/9/2026)",
    "https://corrientes.tur.ar/veda-extraordinaria-en-el-rio-parana-por-concentracion-de-dorados/",
    1
   ],
   [
    "El Litoral: Corrientes extiende la veda extraordinaria en el río Paraná (14/9/2026)",
    "https://www.ellitoral.com.ar/sociedad/2026-9-14-7-43-0-corrientes-extienden-la-veda-extraordinaria-en-el-rio-parana",
    0
   ],
   [
    "Corrientes al Día: Corrientes levantó la veda extraordinaria de pesca en el río Paraná (29/9/2026)",
    "https://corrientesaldia.info/2026/09/29/corrientes-levanto-la-veda-extraordinaria-de-pesca-en-el-rio-parana/",
    0
   ],
   [
    "Elonce: levantaron la veda de pesca por surubíes y dorados en el río Paraná (29/9/2026)",
    "https://www.elonce.com/parana/levantaron-la-veda-extraordinaria-de-pesca-en-el-rio-parana-tras-la-dispersion-de-surubies-y-dorados.htm",
    0
   ],
   [
    "Impacto Corrientes: rige una veda extraordinaria de pesca en el río Paraná frente a la costanera correntina (13/11/2026)",
    "https://www.impactocorrientes.com/notix/noticia/246698/index.html",
    0
   ]
  ],
  "en": [
   [
    "El Litoral: Corrientes establishes extraordinary fishing closure on the Paraná for a surubí concentration (29/5/2026, in Spanish)",
    "https://www.ellitoral.com.ar/sociedad/2026-5-29-16-52-0-corrientes-establece-veda-extraordinaria-de-pesca-en-el-rio-parana-por-concentracion-de-surubies",
    0
   ],
   [
    "Corrientes Ministry of Tourism: notice lifting the closure, Disposición 639/26 (3/6/2026, in Spanish)",
    "https://corrientes.tur.ar/la-pesca-deportiva-retoma-su-actividad-plena-en-corrientes-tras-el-levantamiento-de-la-veda-extraordinaria/",
    1
   ],
   [
    "Corrientes Ministry of Tourism: notice of the extraordinary closure for a dorado concentration (9/9/2026, in Spanish)",
    "https://corrientes.tur.ar/veda-extraordinaria-en-el-rio-parana-por-concentracion-de-dorados/",
    1
   ],
   [
    "El Litoral: Corrientes extends the extraordinary closure on the Paraná (14/9/2026, in Spanish)",
    "https://www.ellitoral.com.ar/sociedad/2026-9-14-7-43-0-corrientes-extienden-la-veda-extraordinaria-en-el-rio-parana",
    0
   ],
   [
    "Corrientes al Día: Corrientes lifted the extraordinary fishing closure on the Paraná (29/9/2026, in Spanish)",
    "https://corrientesaldia.info/2026/09/29/corrientes-levanto-la-veda-extraordinaria-de-pesca-en-el-rio-parana/",
    0
   ],
   [
    "Elonce: fishing closure for surubí and dorado on the Paraná lifted (29/9/2026, in Spanish)",
    "https://www.elonce.com/parana/levantaron-la-veda-extraordinaria-de-pesca-en-el-rio-parana-tras-la-dispersion-de-surubies-y-dorados.htm",
    0
   ],
   [
    "Impacto Corrientes: extraordinary fishing closure on the Paraná off the Corrientes waterfront (13/11/2026, in Spanish)",
    "https://www.impactocorrientes.com/notix/noticia/246698/index.html",
    0
   ]
  ],
  "pt": [
   [
    "El Litoral: Corrientes estabelece defeso extraordinário de pesca no rio Paraná por concentração de surubins (29/5/2026, em espanhol)",
    "https://www.ellitoral.com.ar/sociedad/2026-5-29-16-52-0-corrientes-establece-veda-extraordinaria-de-pesca-en-el-rio-parana-por-concentracion-de-surubies",
    0
   ],
   [
    "Ministério do Turismo de Corrientes: aviso da suspensão do defeso, Disposição 639/26 (3/6/2026, em espanhol)",
    "https://corrientes.tur.ar/la-pesca-deportiva-retoma-su-actividad-plena-en-corrientes-tras-el-levantamiento-de-la-veda-extraordinaria/",
    1
   ],
   [
    "Ministério do Turismo de Corrientes: aviso do defeso extraordinário por concentração de dourados (9/9/2026, em espanhol)",
    "https://corrientes.tur.ar/veda-extraordinaria-en-el-rio-parana-por-concentracion-de-dorados/",
    1
   ],
   [
    "El Litoral: Corrientes estende o defeso extraordinário no rio Paraná (14/9/2026, em espanhol)",
    "https://www.ellitoral.com.ar/sociedad/2026-9-14-7-43-0-corrientes-extienden-la-veda-extraordinaria-en-el-rio-parana",
    0
   ],
   [
    "Corrientes al Día: Corrientes suspendeu o defeso extraordinário de pesca no rio Paraná (29/9/2026, em espanhol)",
    "https://corrientesaldia.info/2026/09/29/corrientes-levanto-la-veda-extraordinaria-de-pesca-en-el-rio-parana/",
    0
   ],
   [
    "Elonce: suspenso o defeso de pesca de surubins e dourados no rio Paraná (29/9/2026, em espanhol)",
    "https://www.elonce.com/parana/levantaron-la-veda-extraordinaria-de-pesca-en-el-rio-parana-tras-la-dispersion-de-surubies-y-dorados.htm",
    0
   ],
   [
    "Impacto Corrientes: defeso extraordinário de pesca no rio Paraná em frente à orla de Corrientes (13/11/2026, em espanhol)",
    "https://www.impactocorrientes.com/notix/noticia/246698/index.html",
    0
   ]
  ]
 },
 "fuentes_esteros": {
  "es": [
   [
    "Ministerio de Turismo de Corrientes: experiencia de pesca en el Iberá",
    "https://corrientes.tur.ar/tour/experienca-de-pesca-ibera/",
    1
   ],
   [
    "Parque Iberá (Gobierno de Corrientes): legislación ambiental y turística y organismos responsables (2015)",
    "https://parqueibera.corrientes.gob.ar/home/legislacion-ambiental-y-turistica-y-organismos-responsables",
    1
   ],
   [
    "El Tránsito Lodge: pesca en el Iberá (página del lodge, 2/6/2026)",
    "https://eltransitoibera.com/pesca/",
    0
   ]
  ],
  "en": [
   [
    "Corrientes Ministry of Tourism: fishing experience in the Iberá (in Spanish)",
    "https://corrientes.tur.ar/tour/experienca-de-pesca-ibera/",
    1
   ],
   [
    "Iberá Park (Government of Corrientes): environmental and tourism legislation and responsible bodies (2015, in Spanish)",
    "https://parqueibera.corrientes.gob.ar/home/legislacion-ambiental-y-turistica-y-organismos-responsables",
    1
   ],
   [
    "El Tránsito Lodge: fishing in the Iberá (the lodge’s page, 2/6/2026, in Spanish)",
    "https://eltransitoibera.com/pesca/",
    0
   ]
  ],
  "pt": [
   [
    "Ministério do Turismo de Corrientes: experiência de pesca no Iberá (em espanhol)",
    "https://corrientes.tur.ar/tour/experienca-de-pesca-ibera/",
    1
   ],
   [
    "Parque Iberá (Governo de Corrientes): legislação ambiental e turística e órgãos responsáveis (2015, em espanhol)",
    "https://parqueibera.corrientes.gob.ar/home/legislacion-ambiental-y-turistica-y-organismos-responsables",
    1
   ],
   [
    "El Tránsito Lodge: pesca no Iberá (página do lodge, 2/6/2026, em espanhol)",
    "https://eltransitoibera.com/pesca/",
    0
   ]
  ]
 },
 "esteros_inicio": {
  "es": "Los esteros son una reserva",
  "en": "The wetlands are a reserve",
  "pt": "Os esteros são uma reserva"
 },
 "esteros_reglas": {
  "es": [
   "Las áreas protegidas del Iberá tienen normas propias. Según el <a href=\"https://corrientes.tur.ar/tour/experienca-de-pesca-ibera/\" rel=\"noopener\">Ministerio de Turismo de Corrientes</a>, dentro de la reserva la pesca deportiva solo se practica en la naciente del río Corrientes y en Concepción del Yaguareté Corá, principalmente con captura y devolución. Según la <a href=\"https://parqueibera.corrientes.gob.ar/home/legislacion-ambiental-y-turistica-y-organismos-responsables\" rel=\"noopener\">página de legislación del Parque Iberá</a> (de 2015), en las áreas naturales protegidas está prohibida la pesca extractiva y permitida la deportiva con devolución, regulada por la Disposición 09/10 de la Dirección de Parques y Reservas (las sanciones, por la Disposición 02/11), y las épocas de veda por especie y la reglamentación de la pesca deportiva están en el Decreto 1304/78.",
   "El lodge <strong>El Tránsito</strong> dice en <a href=\"https://eltransitoibera.com/pesca/\" rel=\"noopener\">su propia página</a> (publicada el 2/6/2026) que la devolución es obligatoria, sin carnada y con anzuelo simple sin rebaba. Para un campo o lodge privado, preguntale qué reglas aplica antes de reservar."
  ],
  "en": [
   "The protected areas of the Iberá have rules of their own. According to the <a href=\"https://corrientes.tur.ar/tour/experienca-de-pesca-ibera/\" rel=\"noopener\">Corrientes Ministry of Tourism</a>, sport fishing inside the reserve is practised only at the source of the Corrientes river and at Concepción del Yaguareté Corá, mainly as catch and release. According to the <a href=\"https://parqueibera.corrientes.gob.ar/home/legislacion-ambiental-y-turistica-y-organismos-responsables\" rel=\"noopener\">Iberá Park legislation page</a> (from 2015), extractive fishing is banned in protected natural areas and sport fishing with release is allowed, regulated by Disposición 09/10 of the Dirección de Parques y Reservas (penalties by Disposición 02/11); closed seasons by species and the sport-fishing regulation are in Decreto 1304/78.",
   "The lodge <strong>El Tránsito</strong> says on <a href=\"https://eltransitoibera.com/pesca/\" rel=\"noopener\">its own page</a> (published 2 June 2026) that release is mandatory, with no bait and a single barbless hook. For a private estate or lodge, ask which rules it applies before you book."
  ],
  "pt": [
   "As áreas protegidas do Iberá têm normas próprias. Segundo o <a href=\"https://corrientes.tur.ar/tour/experienca-de-pesca-ibera/\" rel=\"noopener\">Ministério do Turismo de Corrientes</a>, dentro da reserva a pesca esportiva só é praticada na nascente do rio Corrientes e em Concepción del Yaguareté Corá, principalmente com captura e devolução. Segundo a <a href=\"https://parqueibera.corrientes.gob.ar/home/legislacion-ambiental-y-turistica-y-organismos-responsables\" rel=\"noopener\">página de legislação do Parque Iberá</a> (de 2015), nas áreas naturais protegidas a pesca extrativa é proibida e a esportiva com devolução é permitida, regulada pela Disposição 09/10 da Dirección de Parques y Reservas (as sanções, pela Disposição 02/11); as épocas de defeso por espécie e a regulamentação da pesca esportiva estão no Decreto 1304/78.",
   "O lodge <strong>El Tránsito</strong> diz em <a href=\"https://eltransitoibera.com/pesca/\" rel=\"noopener\">sua própria página</a> (publicada em 2/6/2026) que a devolução é obrigatória, sem isca e com anzol simples sem farpa. Para uma fazenda ou lodge privado, pergunte quais regras aplica antes de reservar."
  ]
 },
 "pend_limpiar": [
  [
   "texto de la Disposición 853/22 en el Boletín Oficial (no se pudo releer el 13/11/2026: el PDF no abrió en el runner; la transcripción es la de las notas ya publicadas); ",
   ""
  ],
  [
   "números de las disposiciones de septiembre de 2026; ",
   ""
  ],
  [
   "normas propias de la reserva o del parque y de los campos privados (no leídas); ",
   ""
  ],
  [
   "; el texto de la Disposición 853/22 en el Boletín Oficial no se pudo releer el 13/11/2026 (el PDF no abrió en el runner): la transcripción es la de las notas ya publicadas",
   ""
  ],
  [
   "coordenadas del pin (las que cargamos salen de la página del operador, redondeadas a dos decimales); ",
   "coordenadas del pin (de Wikipedia, 14/11/2026); "
  ],
  [
   "coordenadas del pin (las que cargamos son aproximadas, de la zona general de los esteros: confirmar); ",
   "coordenadas del pin (centro del sistema de los esteros según Wikipedia, 14/11/2026); "
  ]
 ],
 "pend_borrar_inicio": "PENDIENTE: normas propias de la Reserva Provincial",
 "coord": {
  "ita-ibate": [
   -27.4258,
   -57.3381
  ],
  "esteros-del-ibera": [
   -28.6,
   -57.817
  ]
 },
 "aguas": {
  "es": ", aguas arriba de Goya,",
  "en": ", upstream of Goya,",
  "pt": ", rio acima de Goya,"
 },
 "nota_a": {
  "es": "Desde el 29 de mayo de 2026 rigió una veda extraordinaria en el Paraná entre los km 1232 y 1238 (toda pesca, por una concentración de surubíes): Disposición 618/26 de Corrientes y, según <a href=\"https://www.abc.com.py/interior/2026/06/01/corrientes-y-chaco-decretan-veda-temporal-para-preservar-cardumenes-de-surubi-en-el-rio-parana/\" rel=\"noopener\">ABC Color</a>, Disposición 023 de Chaco (<a href=\"https://www.ellitoral.com.ar/sociedad/2026-5-29-16-52-0-corrientes-establece-veda-extraordinaria-de-pesca-en-el-rio-parana-por-concentracion-de-surubies\" rel=\"noopener\">El Litoral</a>). Según el Ministerio de Turismo de Corrientes, la Disposición 639/26 la levantó a las 12 del 3 de junio de 2026 y volvieron a regir las modalidades de la Disposición 853/22 (<a href=\"https://corrientes.tur.ar/la-pesca-deportiva-retoma-su-actividad-plena-en-corrientes-tras-el-levantamiento-de-la-veda-extraordinaria/\" rel=\"noopener\">aviso</a>).",
  "en": "From 29 May 2026 an extraordinary closure applied on the Paraná between km 1232 and 1238 (all fishing, because of a concentration of surubí): Disposición 618/26 of Corrientes and, according to <a href=\"https://www.abc.com.py/interior/2026/06/01/corrientes-y-chaco-decretan-veda-temporal-para-preservar-cardumenes-de-surubi-en-el-rio-parana/\" rel=\"noopener\">ABC Color</a>, Disposición 023 of Chaco (<a href=\"https://www.ellitoral.com.ar/sociedad/2026-5-29-16-52-0-corrientes-establece-veda-extraordinaria-de-pesca-en-el-rio-parana-por-concentracion-de-surubies\" rel=\"noopener\">El Litoral</a>, in Spanish). According to the Corrientes Ministry of Tourism, Disposición 639/26 lifted it at 12:00 on 3 June 2026 and the rules of Disposición 853/22 applied again (<a href=\"https://corrientes.tur.ar/la-pesca-deportiva-retoma-su-actividad-plena-en-corrientes-tras-el-levantamiento-de-la-veda-extraordinaria/\" rel=\"noopener\">notice</a>, in Spanish).",
  "pt": "Desde 29 de maio de 2026 vigorou um defeso extraordinário no Paraná entre os km 1232 e 1238 (toda a pesca, por uma concentração de surubins): Disposição 618/26 de Corrientes e, segundo <a href=\"https://www.abc.com.py/interior/2026/06/01/corrientes-y-chaco-decretan-veda-temporal-para-preservar-cardumenes-de-surubi-en-el-rio-parana/\" rel=\"noopener\">ABC Color</a>, Disposição 023 do Chaco (<a href=\"https://www.ellitoral.com.ar/sociedad/2026-5-29-16-52-0-corrientes-establece-veda-extraordinaria-de-pesca-en-el-rio-parana-por-concentracion-de-surubies\" rel=\"noopener\">El Litoral</a>, em espanhol). Segundo o Ministério do Turismo de Corrientes, a Disposição 639/26 o suspendeu às 12:00 de 3 de junho de 2026 e voltaram a valer as modalidades da Disposição 853/22 (<a href=\"https://corrientes.tur.ar/la-pesca-deportiva-retoma-su-actividad-plena-en-corrientes-tras-el-levantamiento-de-la-veda-extraordinaria/\" rel=\"noopener\">aviso</a>, em espanhol)."
 },
 "nota_b": {
  "es": "Según el Ministerio de Turismo de Corrientes (<a href=\"https://corrientes.tur.ar/veda-extraordinaria-en-el-rio-parana-por-concentracion-de-dorados/\" rel=\"noopener\">aviso</a>), el 9 de septiembre de 2026 a las 18:00 se dispuso una veda extraordinaria entre los km 1109 y 1150%AGUAS% por una concentración de dorados: toda pesca prohibida, multa de 500 litros de nafta súper e inhabilitación mínima de seis meses (Disposición 931, según La Tribuna TV, 11/9/2026). Según <a href=\"https://www.ellitoral.com.ar/sociedad/2026-9-14-7-43-0-corrientes-extienden-la-veda-extraordinaria-en-el-rio-parana\" rel=\"noopener\">El Litoral</a> (14/9) y <a href=\"https://www.perfil.com/noticias/nea/fijaron-la-veda-extraordinaria-en-un-nuevo-tramo-del-rio-parana-para-proteger-dorados-y-sabalos.phtml\" rel=\"noopener\">Perfil</a> (16/9) se sumaron tramos entre los km 1140 y 1208 y entre los km 1180 y 1239. La Disposición 1054 la levantó a las 13 del 29 de septiembre (según <a href=\"https://corrientesaldia.info/2026/09/29/corrientes-levanto-la-veda-extraordinaria-de-pesca-en-el-rio-parana/\" rel=\"noopener\">Corrientes al Día</a>, <a href=\"https://www.elonce.com/parana/levantaron-la-veda-extraordinaria-de-pesca-en-el-rio-parana-tras-la-dispersion-de-surubies-y-dorados.htm\" rel=\"noopener\">Elonce</a> y <a href=\"https://www.perfil.com/noticias/nea/corrientes-levanto-la-veda-extraordinaria-de-pesca-en-el-rio-parana-que-cambia-desde-hoy.phtml\" rel=\"noopener\">Perfil</a>). Según <a href=\"https://www.impactocorrientes.com/notix/noticia/246698/index.html\" rel=\"noopener\">Impacto Corrientes</a>, la Disposición 1249/26 prohibió desde las 12 del 13 de noviembre toda pesca frente a la Costanera de la ciudad de Corrientes, entre Punta San Sebastián y Punta Tacurú, por un cardumen de manduré cuchara. Las vedas extraordinarias se dictan y se levantan de un día para otro: consultá las vigentes con Recursos Naturales antes de salir.",
  "en": "According to the Corrientes Ministry of Tourism (<a href=\"https://corrientes.tur.ar/veda-extraordinaria-en-el-rio-parana-por-concentracion-de-dorados/\" rel=\"noopener\">notice</a>, in Spanish), on 9 September 2026 at 18:00 an extraordinary closure was ordered between km 1109 and 1150%AGUAS% because of a concentration of dorado: all fishing banned, fine of 500 litres of super petrol and a minimum six-month disqualification (Disposición 931, according to La Tribuna TV, 11/9/2026). According to <a href=\"https://www.ellitoral.com.ar/sociedad/2026-9-14-7-43-0-corrientes-extienden-la-veda-extraordinaria-en-el-rio-parana\" rel=\"noopener\">El Litoral</a> (14/9) and <a href=\"https://www.perfil.com/noticias/nea/fijaron-la-veda-extraordinaria-en-un-nuevo-tramo-del-rio-parana-para-proteger-dorados-y-sabalos.phtml\" rel=\"noopener\">Perfil</a> (16/9), stretches between km 1140 and 1208 and between km 1180 and 1239 were added. Disposición 1054 lifted it at 13:00 on 29 September (according to <a href=\"https://corrientesaldia.info/2026/09/29/corrientes-levanto-la-veda-extraordinaria-de-pesca-en-el-rio-parana/\" rel=\"noopener\">Corrientes al Día</a>, <a href=\"https://www.elonce.com/parana/levantaron-la-veda-extraordinaria-de-pesca-en-el-rio-parana-tras-la-dispersion-de-surubies-y-dorados.htm\" rel=\"noopener\">Elonce</a> and <a href=\"https://www.perfil.com/noticias/nea/corrientes-levanto-la-veda-extraordinaria-de-pesca-en-el-rio-parana-que-cambia-desde-hoy.phtml\" rel=\"noopener\">Perfil</a>). According to <a href=\"https://www.impactocorrientes.com/notix/noticia/246698/index.html\" rel=\"noopener\">Impacto Corrientes</a>, Disposición 1249/26 banned all fishing from 12:00 on 13 November off the Costanera of Corrientes city, between Punta San Sebastián and Punta Tacurú, because of a shoal of manduré cuchara. Extraordinary closures are ordered and lifted from one day to the next: check the current ones with Recursos Naturales before you go.",
  "pt": "Segundo o Ministério do Turismo de Corrientes (<a href=\"https://corrientes.tur.ar/veda-extraordinaria-en-el-rio-parana-por-concentracion-de-dorados/\" rel=\"noopener\">aviso</a>, em espanhol), em 9 de setembro de 2026, às 18:00, foi decretado um defeso extraordinário entre os km 1109 e 1150%AGUAS% por uma concentração de dourados: toda a pesca proibida, multa de 500 litros de gasolina super e inabilitação mínima de seis meses (Disposição 931, segundo La Tribuna TV, 11/9/2026). Segundo <a href=\"https://www.ellitoral.com.ar/sociedad/2026-9-14-7-43-0-corrientes-extienden-la-veda-extraordinaria-en-el-rio-parana\" rel=\"noopener\">El Litoral</a> (14/9) e <a href=\"https://www.perfil.com/noticias/nea/fijaron-la-veda-extraordinaria-en-un-nuevo-tramo-del-rio-parana-para-proteger-dorados-y-sabalos.phtml\" rel=\"noopener\">Perfil</a> (16/9), acrescentaram-se trechos entre os km 1140 e 1208 e entre os km 1180 e 1239. A Disposição 1054 o suspendeu às 13:00 de 29 de setembro (segundo <a href=\"https://corrientesaldia.info/2026/09/29/corrientes-levanto-la-veda-extraordinaria-de-pesca-en-el-rio-parana/\" rel=\"noopener\">Corrientes al Día</a>, <a href=\"https://www.elonce.com/parana/levantaron-la-veda-extraordinaria-de-pesca-en-el-rio-parana-tras-la-dispersion-de-surubies-y-dorados.htm\" rel=\"noopener\">Elonce</a> e <a href=\"https://www.perfil.com/noticias/nea/corrientes-levanto-la-veda-extraordinaria-de-pesca-en-el-rio-parana-que-cambia-desde-hoy.phtml\" rel=\"noopener\">Perfil</a>). Segundo <a href=\"https://www.impactocorrientes.com/notix/noticia/246698/index.html\" rel=\"noopener\">Impacto Corrientes</a>, a Disposição 1249/26 proibiu desde as 12:00 de 13 de novembro toda a pesca em frente à Costanera da cidade de Corrientes, entre Punta San Sebastián e Punta Tacurú, por um cardume de manduré cuchara. Os defesos extraordinários são decretados e suspensos de um dia para o outro: consulte os vigentes com Recursos Naturales antes de sair."
 },
 "notas": {
  "tallas-cupos-vedas": {
   "a": 1,
   "b": 1,
   "goya": 1
  },
  "golden-dorado-guia": {
   "b": 1
  },
  "cuando-pescar-dorado": {
   "b": 1
  },
  "cuando-pescar-surubi": {
   "a": 1
  }
 },
 "precio_tabla": {
  "es": {
   "cab": [
    "Fecha",
    "Qué pasó"
   ],
   "filas": [
    [
     "29 de mayo de 2026",
     "Disposición 618/26: prohíbe toda pesca entre los km 1232 y 1238, por una concentración de surubíes (El Litoral y Ministerio de Turismo de Corrientes)"
    ],
    [
     "3 de junio de 2026, 12:00",
     "Disposición 639/26: levanta esa veda; vuelven a regir las modalidades de la Disposición 853/22 (Ministerio de Turismo de Corrientes)"
    ],
    [
     "9 de septiembre de 2026, 18:00",
     "Veda entre los km 1109 y 1150, por una concentración de dorados (Disposición 931, según La Tribuna TV). Multa de 500 litros de nafta súper e inhabilitación mínima de seis meses (Ministerio de Turismo de Corrientes)"
    ],
    [
     "Septiembre de 2026",
     "Según El Litoral (14/9), se suma el tramo de los km 1140 a 1208; según Perfil (16/9), el de los km 1180 a 1239, jurisdicción de Paso de la Patria"
    ],
    [
     "29 de septiembre de 2026, 13:00",
     "Disposición 1054: levanta la veda y restablece las modalidades de la Disposición 853/22 (Corrientes al Día, Elonce y Perfil)"
    ],
    [
     "13 de noviembre de 2026, 12:00",
     "Disposición 1249/26: toda pesca prohibida frente a la Costanera de la ciudad de Corrientes, entre Punta San Sebastián y Punta Tacurú, por un cardumen de manduré cuchara; alcanza a todas las especies (según Impacto Corrientes)"
    ]
   ],
   "p": "Fuente: el Ministerio de Turismo de Corrientes y la prensa (El Litoral, Perfil, Corrientes al Día, Elonce, Impacto Corrientes, La Tribuna TV), citados en cada fila. Las vedas extraordinarias se dictan y se levantan de un día para otro: consultá las vigentes con la Dirección de Recursos Naturales de Corrientes o con tu guía antes de salir. Desde la Disposición 853/22 el dorado y el surubí se pescan con devolución obligatoria en Corrientes."
  },
  "en": {
   "cab": [
    "Date",
    "What happened"
   ],
   "filas": [
    [
     "29 May 2026",
     "Order 618/26: bans all fishing between km 1232 and 1238, due to a concentration of surubí (El Litoral and the Corrientes Ministry of Tourism)"
    ],
    [
     "3 June 2026, 12:00",
     "Order 639/26: lifts that ban; the rules of Order 853/22 apply again (Corrientes Ministry of Tourism)"
    ],
    [
     "9 September 2026, 18:00",
     "Ban between km 1109 and 1150, due to a concentration of dorado (Order 931, according to La Tribuna TV). Fine of 500 litres of super petrol and a minimum six-month disqualification (Corrientes Ministry of Tourism)"
    ],
    [
     "September 2026",
     "According to El Litoral (14/9), the stretch from km 1140 to 1208 is added; according to Perfil (16/9), the one from km 1180 to 1239, Paso de la Patria jurisdiction"
    ],
    [
     "29 September 2026, 13:00",
     "Order 1054: lifts the ban and reinstates the rules of Order 853/22 (Corrientes al Día, Elonce and Perfil)"
    ],
    [
     "13 November 2026, 12:00",
     "Order 1249/26: all fishing banned off the Costanera of Corrientes city, between Punta San Sebastián and Punta Tacurú, because of a shoal of manduré cuchara; it covers all species (according to Impacto Corrientes)"
    ]
   ],
   "p": "Source: the Corrientes Ministry of Tourism and the press (El Litoral, Perfil, Corrientes al Día, Elonce, Impacto Corrientes, La Tribuna TV), cited in each row. Extraordinary bans are ordered and lifted from one day to the next: check the current ones with the Corrientes Department of Natural Resources or your guide before heading out. Since Order 853/22, dorado and surubí must be released in Corrientes."
  },
  "pt": {
   "cab": [
    "Data",
    "O que aconteceu"
   ],
   "filas": [
    [
     "29 de maio de 2026",
     "Disposição 618/26: proíbe toda a pesca entre os km 1232 e 1238, por concentração de surubins (El Litoral e Ministério do Turismo de Corrientes)"
    ],
    [
     "3 de junho de 2026, 12:00",
     "Disposição 639/26: suspende essa proibição; voltam a valer as modalidades da Disposição 853/22 (Ministério do Turismo de Corrientes)"
    ],
    [
     "9 de setembro de 2026, 18:00",
     "Proibição entre os km 1109 e 1150, por concentração de dourados (Disposição 931, segundo La Tribuna TV). Multa de 500 litros de gasolina super e inabilitação mínima de seis meses (Ministério do Turismo de Corrientes)"
    ],
    [
     "Setembro de 2026",
     "Segundo El Litoral (14/9), acrescenta-se o trecho dos km 1140 a 1208; segundo Perfil (16/9), o dos km 1180 a 1239, jurisdição de Paso de la Patria"
    ],
    [
     "29 de setembro de 2026, 13:00",
     "Disposição 1054: suspende a proibição e restabelece as modalidades da Disposição 853/22 (Corrientes al Día, Elonce e Perfil)"
    ],
    [
     "13 de novembro de 2026, 12:00",
     "Disposição 1249/26: toda a pesca proibida em frente à Costanera da cidade de Corrientes, entre Punta San Sebastián e Punta Tacurú, por um cardume de manduré cuchara; vale para todas as espécies (segundo Impacto Corrientes)"
    ]
   ],
   "p": "Fonte: o Ministério do Turismo de Corrientes e a imprensa (El Litoral, Perfil, Corrientes al Día, Elonce, Impacto Corrientes, La Tribuna TV), citados em cada linha. Os defesos extraordinários são decretados e suspensos de um dia para o outro: consulte os vigentes com a Direção de Recursos Naturais de Corrientes ou com o seu guia antes de sair. Desde a Disposição 853/22, o dourado e o surubim só podem ser pescados com devolução obrigatória em Corrientes."
  }
 },
 "precio_h2": {
  "es": "¿Hay veda en el Paraná en Corrientes ahora?",
  "en": "Is there a fishing ban on the Paraná in Corrientes now?",
  "pt": "Há proibição no Paraná em Corrientes agora?"
 },
 "licencia": {
  "es": [
   "sí la incluyen, según el operador o agencias, Itatí y los lodges del Iberá de la tabla, y una agencia dice lo mismo del lodge del Alto Paraná de US$ 7.000",
   "sí la incluye, según agencias, Itatí (la página de su operador no menciona la licencia); según el operador o agencias, también los lodges del Iberá de la tabla, y una agencia dice lo mismo del lodge del Alto Paraná de US$ 7.000"
  ],
  "en": [
   "Itatí and the Iberá lodges in the table do include it, according to the operator or agencies, and one agency says the same of the US$ 7,000 Upper Paraná lodge",
   "according to agencies, Itatí does include it (its operator’s page does not mention the license); according to the operator or agencies, so do the Iberá lodges in the table, and one agency says the same of the US$ 7,000 Upper Paraná lodge"
  ],
  "pt": [
   "incluem, segundo o operador ou agências, Itatí e os lodges do Iberá da tabela, e uma agência diz o mesmo do lodge do Alto Paraná de US$ 7.000",
   "segundo agências, Itatí a inclui (a página do seu operador não menciona a licença); segundo o operador ou agências, também os lodges do Iberá da tabela, e uma agência diz o mesmo do lodge do Alto Paraná de US$ 7.000"
  ]
 }
}
JSON
, true );
if ( ! is_array( $D ) ) {
	WP_CLI::error( 'El JSON de datos no se pudo leer: ' . json_last_error_msg() );
}
$langs = [ 'es', 'en', 'pt' ];

// ---- helpers --------------------------------------------------------------------------------------------------------------------------
function pm_cc_post( string $tipo, string $clave, string $i ): ?WP_Post {
	foreach ( get_posts( [ 'post_type' => $tipo, 'post_status' => 'publish', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ) as $p ) {
		if ( pm_idioma_de( $p->ID ) === $i ) {
			return $p;
		}
	}
	return null;
}
function pm_cc_p( string $h ): string {
	return "<!-- wp:paragraph -->\n<p>{$h}</p>\n<!-- /wp:paragraph -->";
}
function pm_cc_ul( array $items ): string {
	$li = implode( '', array_map( fn( $x ) => "<!-- wp:list-item --><li>{$x}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_cc_tabla( array $cab, array $filas ): string {
	$th = implode( '', array_map( fn( $c ) => "<th>{$c}</th>", $cab ) );
	$tr = implode( '', array_map( fn( $f ) => '<tr>' . implode( '', array_map( fn( $c ) => "<td>{$c}</td>", $f ) ) . '</tr>', $filas ) );
	return "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr>{$th}</tr></thead><tbody>{$tr}</tbody></table></figure>\n<!-- /wp:table -->";
}
/** {n:clave} -> permalink of that note in the language (Spanish if it has no translation). */
function pm_cc_resolver( string $html, string $i ): string {
	return preg_replace_callback( '/\{n:([a-z0-9-]+)\}/', function ( $m ) use ( $i ) {
		$p = pm_cc_post( 'pm_nota', $m[1], $i ) ?? pm_cc_post( 'pm_nota', $m[1], 'es' );
		if ( ! $p ) {
			WP_CLI::error( "No encuentro la nota {$m[1]}." );
		}
		return esc_url( get_permalink( $p->ID ) );
	}, $html );
}
/** Replaces everything between the h2 with one of these titles and the next heading (or the end) by $nuevo. Null if the heading is not there. */
function pm_cc_seccion( string $c, array $titulos, string $nuevo ): ?string {
	foreach ( $titulos as $t ) {
		$re = '#(<!-- wp:heading[^>]*-->\s*<h2[^>]*>' . preg_quote( $t, '#' ) . '</h2>\s*<!-- /wp:heading -->)(.*?)(?=<!-- wp:heading|\z)#us';
		if ( preg_match( $re, $c, $m, PREG_OFFSET_CAPTURE ) ) {
			$resto = substr( $c, $m[2][1] + strlen( $m[2][0] ) );
			return substr( $c, 0, $m[1][1] + strlen( $m[1][0] ) ) . "\n\n" . $nuevo . ( '' === $resto ? '' : "\n\n" . $resto );
		}
	}
	return null;
}
/** The list items of a note that talk about a closure and contain $aguja: the first becomes $texto, the others go. */
function pm_cc_li( string $c, string $aguja, string $texto, array &$viejos ): string {
	$n   = 0;
	$out = preg_replace_callback( '#<li>((?:(?!</li>).)*)</li>#us', function ( $m ) use ( $aguja, $texto, &$n, &$viejos ) {
		if ( false === strpos( $m[1], $aguja ) || ! preg_match( '/veda|closure|defeso|Disposi/iu', $m[1] ) ) {
			return $m[0];
		}
		$n++;
		$viejos[] = mb_substr( wp_strip_all_tags( $m[1] ), 0, 160 );
		return 1 === $n ? '<li>' . $texto . '</li>' : '';
	}, $c );
	return preg_replace( '#<!-- wp:list-item -->\s*<!-- /wp:list-item -->\s*#u', '', $out );
}
/** Source rows as the repeater wants them. */
function pm_cc_fuentes( int $id ): array {
	return array_map( fn( $f ) => [
		'nombre'     => (string) ( $f['nombre'] ?? '' ),
		'url'        => (string) ( $f['url'] ?? '' ),
		'oficial'    => empty( $f['oficial'] ) ? 0 : 1,
		'consultada' => str_replace( '-', '', (string) ( $f['consultada'] ?? '' ) ),
	], array_values( array_filter( pm_filas( 'fuentes', $id ), 'is_array' ) ) );
}
/** New rows go right after the last Perfil row (the old closure articles); rows whose URL is already there are skipped. */
function pm_cc_fuentes_nuevas( array $filas, array $nuevas, string $hoy ): array {
	$urls = array_column( $filas, 'url' );
	$pos  = count( $filas );
	foreach ( $filas as $k => $f ) {
		if ( false !== strpos( $f['url'], 'perfil.com' ) ) {
			$pos = $k + 1;
		}
	}
	$add = [];
	foreach ( $nuevas as [ $nombre, $url, $oficial ] ) {
		if ( ! in_array( $url, $urls, true ) ) {
			$add[] = [ 'nombre' => $nombre, 'url' => $url, 'oficial' => $oficial, 'consultada' => $hoy ];
		}
	}
	return array_merge( array_slice( $filas, 0, $pos ), $add, array_slice( $filas, $pos ) );
}
function pm_cc_faqs( int $id ): array {
	return array_map( fn( $f ) => [ 'pregunta' => (string) ( $f['pregunta'] ?? '' ), 'respuesta' => (string) ( $f['respuesta'] ?? '' ) ], array_values( array_filter( pm_filas( 'faqs', $id ), 'is_array' ) ) );
}
const PM_CC_VIEJO = '/n[úu]mero (de (la )?disposici[oó]n )?pendiente|number pending|order number pending|n[úu]mero da disposi[cç][aã]o pendente|Disposici[oó]n 942|no le[ií]mos las disposiciones|solo las notas de Perfil|we have not read the orders|n[aã]o lemos as disposi/iu';

// ---- plan ----------------------------------------------------------------------------------------------------------------------------
$plan = [];
$aviso = 0;

// 1. The three guides.
foreach ( $D['guias'] as $clave => $gd ) {
	foreach ( $langs as $i ) {
		$g = pm_cc_post( 'pm_destino', $clave, $i );
		if ( ! $g ) {
			WP_CLI::warning( "guía {$clave} ({$i}): no existe" );
			$aviso++;
			continue;
		}
		$id = $g->ID;
		$c  = $g->post_content;
		$n  = $c;
		// 1a. Closure section.
		$bloques = [ pm_cc_p( $D['intro'][ $i ] ), pm_cc_ul( $D['items'][ $i ] ) ];
		$bloques[] = pm_cc_p( null === $gd['n'] ? $D['lugar']['esteros'][ $i ] : sprintf( $D['lugar']['rio'][ $i ], $gd['n'], $gd['km'] ) );
		$bloques[] = pm_cc_p( pm_cc_resolver( $D['cierre'][ $i ], $i ) );
		$r = pm_cc_seccion( $n, $D['sec'][ $i ], implode( "\n\n", $bloques ) );
		if ( null === $r ) {
			WP_CLI::warning( "guía {$clave} ({$i}) #{$id}: no encontré la sección de vedas; no la toco" );
			$aviso++;
		} else {
			$n = $r;
		}
		// 1b. Esteros: the rules paragraph.
		if ( null === $gd['n'] ) {
			$re = '#<!-- wp:paragraph -->\s*<p>' . preg_quote( $D['esteros_inicio'][ $i ], '#' ) . '.*?</p>\s*<!-- /wp:paragraph -->#us';
			if ( preg_match( $re, $n ) ) {
				$n = preg_replace_callback( $re, fn() => implode( "\n\n", array_map( 'pm_cc_p', $D['esteros_reglas'][ $i ] ) ), $n, 1 );
			} elseif ( false === strpos( $n, '1304/78' ) ) {
				WP_CLI::warning( "guía {$clave} ({$i}) #{$id}: no encontré el párrafo de la reserva; no lo toco" );
				$aviso++;
			}
			$n = preg_replace( '#<!-- wp:quote -->\s*<blockquote class="wp-block-quote"><!-- wp:paragraph -->\s*<p>' . preg_quote( $D['pend_borrar_inicio'], '#' ) . '.*?<!-- /wp:quote -->\s*#us', '', $n );
		}
		// 1c. Hidden editor notes.
		foreach ( $D['pend_limpiar'] as [ $viejo, $nuevo ] ) {
			$n = str_replace( $viejo, $nuevo, $n );
		}
		$campos = [];
		// 1d. Fields.
		$res = (string) pm_campo( 'reglamento_resumen', $id );
		if ( false !== strpos( $res, $D['resumen_viejo'][ $i ] ) ) {
			$res = str_replace( $D['resumen_viejo'][ $i ], $D['resumen_nuevo'][ $i ] . ( null === $gd['n'] ? $D['resumen_esteros'][ $i ] : '' ), $res );
			$campos['reglamento_resumen'] = $res;
		} elseif ( false === strpos( $res, $D['resumen_nuevo'][ $i ] ) ) {
			WP_CLI::warning( "guía {$clave} ({$i}) #{$id}: reglamento_resumen no trae la frase de las vedas; no lo toco" );
			$aviso++;
		}
		$faqs_viejas = [];
		if ( null !== $gd['n'] ) {
			$faqs = pm_cc_faqs( $id );
			$cambia = false;
			foreach ( $faqs as $k => $f ) {
				if ( preg_match( $D['faq_rx'], $f['pregunta'] ) ) {
					$nueva = sprintf( $D['faq'][ $i ], $gd['n'], $gd['km'] );
					if ( $f['respuesta'] !== $nueva ) {
						$faqs_viejas[] = mb_substr( $f['respuesta'], 0, 120 );
						$faqs[ $k ]['respuesta'] = $nueva;
						$cambia = true;
					}
				}
			}
			if ( $cambia ) {
				$campos['faqs'] = $faqs;
			} elseif ( ! array_filter( $faqs, fn( $f ) => false !== strpos( $f['respuesta'], 'Impacto Corrientes' ) ) ) {
				WP_CLI::warning( "guía {$clave} ({$i}) #{$id}: no encontré la pregunta de las vedas; no la toco" );
				$aviso++;
			}
		}
		$filas = pm_cc_fuentes( $id );
		$nuevas = array_merge( $D['fuentes_veda'][ $i ], null === $gd['n'] ? $D['fuentes_esteros'][ $i ] : [] );
		$fn = pm_cc_fuentes_nuevas( $filas, $nuevas, $hoy );
		if ( count( $fn ) !== count( $filas ) ) {
			$campos['fuentes'] = $fn;
		}
		if ( 'es' === $i && isset( $D['coord'][ $clave ] ) ) {
			[ $la, $lo ] = $D['coord'][ $clave ];
			if ( abs( (float) pm_campo( 'lat', $id ) - $la ) > 0.00005 || abs( (float) pm_campo( 'lon', $id ) - $lo ) > 0.00005 ) {
				$campos['lat'] = $la;
				$campos['lon'] = $lo;
			}
		}
		$plan[] = [ 'tipo' => 'guía', 'clave' => $clave, 'i' => $i, 'id' => $id, 'slug' => $g->post_name, 'viejo' => $c, 'nuevo' => $n, 'campos' => $campos, 'faqs_viejas' => $faqs_viejas ];
	}
}

// 2. Notes with the closure list items.
foreach ( $D['notas'] as $clave => $cual ) {
	foreach ( $langs as $i ) {
		$p = pm_cc_post( 'pm_nota', $clave, $i );
		if ( ! $p ) {
			WP_CLI::warning( "nota {$clave} ({$i}): no existe" );
			$aviso++;
			continue;
		}
		$n      = $p->post_content;
		$viejos = [];
		foreach ( [ 'a' => '1232', 'b' => '1109' ] as $k => $aguja ) {
			if ( empty( $cual[ $k ] ) ) {
				continue;
			}
			$texto  = str_replace( '%AGUAS%', ( 'b' === $k && ! empty( $cual['goya'] ) ) ? $D['aguas'][ $i ] : '', $D[ 'nota_' . $k ][ $i ] );
			$antes  = count( $viejos );
			$n      = pm_cc_li( $n, $aguja, $texto, $viejos );
			if ( count( $viejos ) === $antes ) {
				WP_CLI::warning( "nota {$clave} ({$i}) #{$p->ID}: no encontré el ítem «{$k}» de la veda; no lo toco" );
				$aviso++;
			}
		}
		$campos = [];
		if ( 'tallas-cupos-vedas' === $clave ) {
			$filas = pm_cc_fuentes( $p->ID );
			$fn    = pm_cc_fuentes_nuevas( $filas, $D['fuentes_veda'][ $i ], $hoy );
			if ( count( $fn ) !== count( $filas ) ) {
				$campos['fuentes'] = $fn;
			}
		}
		$plan[] = [ 'tipo' => 'nota', 'clave' => $clave, 'i' => $i, 'id' => $p->ID, 'slug' => $p->post_name, 'viejo' => $p->post_content, 'nuevo' => $n, 'campos' => $campos, 'viejos' => $viejos, 'cual' => $cual ];
	}
}

// 3. The two price notes.
foreach ( $langs as $i ) {
	$p = pm_cc_post( 'pm_nota', 'viaje-dorado-amigos-precios', $i );
	if ( ! $p ) {
		WP_CLI::warning( "nota viaje-dorado-amigos-precios ({$i}): no existe" );
		$aviso++;
	} else {
		$t = $D['precio_tabla'][ $i ];
		$r = pm_cc_seccion( $p->post_content, [ $D['precio_h2'][ $i ] ], pm_cc_tabla( $t['cab'], $t['filas'] ) . "\n\n" . pm_cc_p( $t['p'] ) );
		if ( null === $r ) {
			WP_CLI::warning( "nota viaje-dorado-amigos-precios ({$i}) #{$p->ID}: no encontré la sección de la veda; no la toco" );
			$aviso++;
			$r = $p->post_content;
		}
		$plan[] = [ 'tipo' => 'nota', 'clave' => 'viaje-dorado-amigos-precios', 'i' => $i, 'id' => $p->ID, 'slug' => $p->post_name, 'viejo' => $p->post_content, 'nuevo' => $r, 'campos' => [], 'viejos' => [], 'cual' => [ 'precio' => 1 ] ];
	}
	$p = pm_cc_post( 'pm_nota', 'precio-viaje-dorado-lodge', $i );
	if ( ! $p ) {
		WP_CLI::warning( "nota precio-viaje-dorado-lodge ({$i}): no existe" );
		$aviso++;
	} else {
		[ $viejo, $nuevo ] = $D['licencia'][ $i ];
		$n = $p->post_content;
		if ( false !== strpos( $n, $viejo ) ) {
			$n = str_replace( $viejo, $nuevo, $n );
		} elseif ( false === strpos( $n, $nuevo ) ) {
			WP_CLI::warning( "nota precio-viaje-dorado-lodge ({$i}) #{$p->ID}: no encontré la frase de la licencia de Itatí; no la toco" );
			$aviso++;
		}
		$plan[] = [ 'tipo' => 'nota', 'clave' => 'precio-viaje-dorado-lodge', 'i' => $i, 'id' => $p->ID, 'slug' => $p->post_name, 'viejo' => $p->post_content, 'nuevo' => $n, 'campos' => [], 'viejos' => [], 'cual' => [ 'licencia' => 1 ] ];
	}
}

// ---- report ---------------------------------------------------------------------------------------------------------------------------
$cambios = 0;
foreach ( $plan as $x ) {
	$cambia = $x['nuevo'] !== $x['viejo'] || $x['campos'];
	$cambia && $cambios++;
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": {$x['tipo']} {$x['clave']} ({$x['i']}) #{$x['id']} {$x['slug']}: " . ( $cambia ? ( $x['nuevo'] !== $x['viejo'] ? 'contenido ' . strlen( $x['viejo'] ) . ' → ' . strlen( $x['nuevo'] ) . ' bytes' : 'contenido igual' ) . ( $x['campos'] ? ' · campos: ' . implode( ', ', array_keys( $x['campos'] ) ) : '' ) : 'ya está, no toco nada' ) );
	foreach ( $x['viejos'] ?? [] as $v ) {
		WP_CLI::log( "    ítem que se reemplaza: {$v}" );
	}
	foreach ( $x['faqs_viejas'] ?? [] as $v ) {
		WP_CLI::log( "    respuesta que se reemplaza: {$v}" );
	}
	if ( $cambia && ! $aplicar && ! empty( $x['campos']['lat'] ) ) {
		WP_CLI::log( "    pin: " . pm_campo( 'lat', $x['id'] ) . ', ' . pm_campo( 'lon', $x['id'] ) . " → {$x['campos']['lat']}, {$x['campos']['lon']}" );
	}
}
// What would be left in the new texts: nothing of the old wording.
foreach ( $plan as $x ) {
	if ( preg_match( PM_CC_VIEJO, $x['nuevo'] . wp_json_encode( $x['campos'], JSON_UNESCAPED_UNICODE ), $m ) ) {
		WP_CLI::warning( "{$x['tipo']} {$x['clave']} ({$x['i']}) #{$x['id']}: queda texto viejo: «{$m[0]}»" );
		$aviso++;
	}
}
if ( ! $aplicar ) {
	WP_CLI::log( "SIMULACRO: no se escribió nada ({$cambios} cambios planeados, {$aviso} avisos)" );
	return;
}

// ---- apply ---------------------------------------------------------------------------------------------------------------------------
foreach ( $plan as $x ) {
	if ( $x['nuevo'] === $x['viejo'] && ! $x['campos'] ) {
		continue;
	}
	if ( $x['nuevo'] !== $x['viejo'] ) {
		$r = wp_update_post( [ 'ID' => $x['id'], 'post_content' => wp_slash( $x['nuevo'] ) ], true );
		if ( is_wp_error( $r ) ) {
			WP_CLI::error( "{$x['clave']} ({$x['i']}): " . $r->get_error_message() );
		}
	}
	foreach ( $x['campos'] as $k => $v ) {
		update_field( $k, $v, $x['id'] );
	}
	update_field( 'actualizado', $hoy, $x['id'] );
	update_post_meta( $x['id'], 'es' === $x['i'] ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $x['i'] ? 0 : 1 ) );
}

// ---- read back -----------------------------------------------------------------------------------------------------------------------
$ok = true;
$marca_a = [ 'es' => '12 del 3 de junio de 2026', 'en' => '12:00 on 3 June 2026', 'pt' => '12:00 de 3 de junho de 2026' ];
foreach ( $plan as $x ) {
	$p  = get_post( $x['id'] );
	$i  = $x['i'];
	$c  = $p->post_content;
	$vis = apply_filters( 'the_content', $c );
	$bb = ! preg_match( PM_CC_VIEJO, $c );
	$det = [];
	if ( 'guía' === $x['tipo'] ) {
		$bb = $bb && false !== strpos( $c, '1249/26' ) && false !== strpos( $c, '1054/26' ) && false === strpos( $vis, 'PENDIENTE' ) && false === strpos( $c, 'el PDF no abrió' );
		$det[] = 'sección de vedas con 618/26, 639/26, 9/9, 1054/26 y 1249/26';
		$res = (string) pm_campo( 'reglamento_resumen', $x['id'] );
		$b2  = false !== strpos( $res, $D['resumen_nuevo'][ $i ] ) && false === strpos( $res, $D['resumen_viejo'][ $i ] );
		$det[] = 'reglamento_resumen ' . ( $b2 ? 'ok' : 'FALLÓ' );
		$bb = $bb && $b2;
		$u = array_column( pm_cc_fuentes( $x['id'] ), 'url' );
		foreach ( $D['fuentes_veda'][ $i ] as $f ) {
			$bb = $bb && in_array( $f[1], $u, true );
		}
		$det[] = 'fuentes ' . count( $u );
		if ( 'esteros-del-ibera' === $x['clave'] ) {
			foreach ( [ '1304/78', '09/10', 'eltransitoibera.com/pesca/', 'parqueibera.corrientes.gob.ar' ] as $t ) {
				$bb = $bb && false !== strpos( $c, $t );
			}
			$bb = $bb && false === strpos( $c, $D['esteros_inicio'][ $i ] ) && false === strpos( $c, $D['pend_borrar_inicio'] );
			foreach ( $D['fuentes_esteros'][ $i ] as $f ) {
				$bb = $bb && in_array( $f[1], $u, true );
			}
			$det[] = 'reglas del Iberá (Ministerio, legislación del Parque, El Tránsito)';
		} else {
			$gd = $D['guias'][ $x['clave'] ];
			$q  = array_values( array_filter( pm_cc_faqs( $x['id'] ), fn( $f ) => preg_match( $D['faq_rx'], $f['pregunta'] ) ) );
			$b3 = $q && $q[0]['respuesta'] === sprintf( $D['faq'][ $i ], $gd['n'], $gd['km'] );
			$det[] = 'FAQ de vedas ' . ( $b3 ? 'ok' : 'FALLÓ' ) . ", km {$gd['km']} aguas arriba";
			$bb = $bb && $b3 && false !== strpos( $c, 'km ' . $gd['km'] );
		}
		if ( isset( $x['campos']['lat'] ) || ( 'es' === $i && isset( $D['coord'][ $x['clave'] ] ) ) ) {
			[ $la, $lo ] = $D['coord'][ $x['clave'] ];
			$b4 = abs( (float) pm_campo( 'lat', $x['id'] ) - $la ) < 0.0001 && abs( (float) pm_campo( 'lon', $x['id'] ) - $lo ) < 0.0001;
			$det[] = 'pin ' . pm_campo( 'lat', $x['id'] ) . ', ' . pm_campo( 'lon', $x['id'] ) . ( $b4 ? '' : ' FALLÓ' );
			$bb = $bb && $b4;
		}
	} elseif ( ! empty( $x['cual']['precio'] ) ) {
		$bb = $bb && false !== strpos( $c, '1249/26' ) && false !== strpos( $c, '931' ) && false === strpos( $c, 'no lo leímos en el Boletín' );
		$det[] = 'tabla de vedas con 1249/26';
	} elseif ( ! empty( $x['cual']['licencia'] ) ) {
		$bb = $bb && false !== strpos( $c, $D['licencia'][ $i ][1] ) && false === strpos( $c, $D['licencia'][ $i ][0] );
		$det[] = 'licencia de Itatí según agencias';
	} else {
		if ( ! empty( $x['cual']['a'] ) ) {
			$bb = $bb && false !== strpos( $c, $marca_a[ $i ] );
			$det[] = '639/26 a las 12 del 3/6';
		}
		if ( ! empty( $x['cual']['b'] ) ) {
			$bb = $bb && false !== strpos( $c, '1249/26' ) && false !== strpos( $c, '1054' );
			$det[] = 'cronología del dorado hasta 1249/26';
		}
		if ( 'tallas-cupos-vedas' === $x['clave'] ) {
			$u  = array_column( pm_cc_fuentes( $x['id'] ), 'url' );
			foreach ( $D['fuentes_veda'][ $i ] as $f ) {
				$bb = $bb && in_array( $f[1], $u, true );
			}
			$det[] = 'fuentes ' . count( $u );
		}
	}
	WP_CLI::log( ( $bb ? 'VERIFIED' : 'FALLÓ' ) . ": {$x['tipo']} {$x['clave']} ({$i}) #{$x['id']} {$p->post_name} " . get_permalink( $x['id'] ) . ' · ' . implode( ' · ', $det ) );
	$ok = $ok && $bb;
}
$ok || WP_CLI::error( 'algo no se leyó de vuelta como se esperaba.' );
WP_CLI::log( 'APLICADO' );
