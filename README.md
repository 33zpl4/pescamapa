# Pescamapa

Destinos, especies y guías para pescar en Argentina. Astro (estático) en
Cloudflare Workers.

```sh
npm install
npm run dev      # http://localhost:4321
npm run build    # valida los datos y genera dist/
```

- **Editar sin programar**: https://app.pagescms.org (configurado en
  `.pages.yml`). Guía para editores: `docs/guia-editor.md`.
- Reglas del modelo de datos y de publicación: `CLAUDE.md`.
- Deploy: Cloudflare → Workers & Pages → Create → Import a repository →
  `33zpl4/pescamapa`. Comando de deploy: `npx wrangler deploy` (lee
  `wrangler.jsonc`, que compila y sube `dist/`).
