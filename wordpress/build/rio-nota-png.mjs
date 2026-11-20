// SVG -> PNG for the Goya note infographics (20/11/2026). node wordpress/build/rio-nota-png.mjs [carpeta-svg] [carpeta-png]
// Chromium (playwright) with the brand fonts of the theme embedded; 1200 x 900 viewBox at 4/3 = 1600 x 1200 px (the repo's photo limit is 1600 px wide).
import { readFileSync, writeFileSync, mkdirSync, readdirSync } from 'node:fs';
import { createRequire } from 'node:module';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { spawnSync } from 'node:child_process';
const require = createRequire(import.meta.url);
const { chromium } = require('playwright');
const aqui = dirname(fileURLToPath(import.meta.url));
const fuentes = resolve(aqui, '../themes/pescamapa/assets/fonts');
const ent = resolve(process.argv[2] || join(aqui, 'rio-nota-goya'));
const sal = resolve(process.argv[3] || join(aqui, '../fotos'));
mkdirSync(sal, { recursive: true });
const b64 = (f) => readFileSync(join(fuentes, f)).toString('base64');
const css = `
@font-face{font-family:Outfit;font-weight:100 900;src:url(data:font/woff2;base64,${b64('outfit-latin-wght-normal.woff2')})}
@font-face{font-family:'Playfair Display';font-weight:400 900;src:url(data:font/woff2;base64,${b64('playfair-display-latin-wght-normal.woff2')})}
@font-face{font-family:'Bebas Neue';font-weight:400;src:url(data:font/woff2;base64,${b64('bebas-neue-latin-400-normal.woff2')})}
html,body{margin:0;background:#fbfaf6}svg{display:block}`;
const nombres = { '1-goya-ano.svg': 'parana-goya-2026-el-ano.png', '2-goya-20-de-noviembre.svg': 'parana-goya-20-de-noviembre.png', '3-crecida-rio-abajo.svg': 'parana-crecida-rio-abajo.png' };
const b = await chromium.launch();
const p = await b.newPage({ viewport: { width: 1200, height: 900 }, deviceScaleFactor: 4 / 3 });
for (const f of readdirSync(ent).filter((x) => x.endsWith('.svg')).sort()) {
  const svg = readFileSync(join(ent, f), 'utf8');
  await p.setContent(`<!doctype html><meta charset="utf-8"><style>${css}</style>${svg}`);
  await p.evaluate(() => document.fonts.ready);
  const out = join(sal, nombres[f]);
  writeFileSync(out, await p.screenshot({ type: 'png', clip: { x: 0, y: 0, width: 1200, height: 900 } }));
  // flat colours: a 96-colour palette keeps the text sharp and each PNG under ~300 KB (Pillow)
  spawnSync('python3', ['-I', '-c', 'import sys;from PIL import Image;i=Image.open(sys.argv[1]).convert("RGB").quantize(96,method=Image.Quantize.MEDIANCUT,dither=Image.Dither.NONE);i.save(sys.argv[1],optimize=True)', out], { stdio: 'inherit' });
  console.log('png', out);
}
await b.close();
