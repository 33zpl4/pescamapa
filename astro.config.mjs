// @ts-check
import { defineConfig } from 'astro/config';
import sitemap from '@astrojs/sitemap';
import { borradores } from './src/lib/borradores.mjs';

const site = 'https://pescamapa.com';
const ocultos = borradores();

export default defineConfig({
	site,
	trailingSlash: 'always',
	// Spanish at the root, Brazilian Portuguese under /pt/, English under /en/.
	// Pages are routed by folder; hreflang comes from the layout.
	i18n: { locales: ['es', 'pt', 'en'], defaultLocale: 'es' },
	integrations: [
		// Drafts are noindex and must never reach the sitemap.
		sitemap({ filter: (page) => !ocultos.some((p) => page === `${site}${p}`) }),
	],
});
