// @ts-check
import { defineConfig } from 'astro/config';
import sitemap from '@astrojs/sitemap';
import { borradores } from './src/lib/borradores.mjs';

const site = 'https://pescamapa.com';
const ocultos = borradores();

export default defineConfig({
	site,
	trailingSlash: 'always',
	// Spanish first. pt and en get their own URL trees (/pt/, /en/) when the
	// first translated page exists; hreflang is emitted from the layout.
	i18n: { locales: ['es'], defaultLocale: 'es' },
	integrations: [
		// Drafts are noindex and must never reach the sitemap.
		sitemap({ filter: (page) => !ocultos.some((p) => page === `${site}${p}`) }),
	],
});
