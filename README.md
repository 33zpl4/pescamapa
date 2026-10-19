# Pescamapa

Destinos, especies y guías para pescar en Argentina. Astro (estático) en
Cloudflare Workers.

```sh
npm install
npm run dev      # http://localhost:4321
npm run build    # valida los datos y genera dist/
```

- Agregar un destino: copiar `src/content/destinos/goya.md`, cambiar los
  campos y dejar `estado: borrador` hasta verificarlo. Ver `CLAUDE.md`.
- Deploy: Cloudflare → Workers & Pages → Create → Import a repository →
  `33zpl4/pescamapa`. Comando de deploy: `npx wrangler deploy` (lee
  `wrangler.jsonc`, que compila y sube `dist/`).
