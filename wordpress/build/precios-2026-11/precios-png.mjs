// SVG -> PNG 1600 x 1200 de las infografias de la nota de precios. node wordpress/build/precios-2026-11/precios-png.mjs
import { readFileSync, writeFileSync } from 'node:fs';
import { createRequire } from 'node:module';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { spawnSync } from 'node:child_process';
const require = createRequire(import.meta.url);
const { chromium } = require('playwright');
const aqui = dirname(fileURLToPath(import.meta.url));
const fuentes = resolve(aqui, '../../themes/pescamapa/assets/fonts');
const sal = resolve(aqui, '../../fotos');
const b64 = (f) => readFileSync(join(fuentes, f)).toString('base64');
const css = `
@font-face{font-family:Outfit;font-weight:100 900;src:url(data:font/woff2;base64,${b64('outfit-latin-wght-normal.woff2')})}
@font-face{font-family:'Playfair Display';font-weight:400 900;src:url(data:font/woff2;base64,${b64('playfair-display-latin-wght-normal.woff2')})}
@font-face{font-family:'Bebas Neue';font-weight:400;src:url(data:font/woff2;base64,${b64('bebas-neue-latin-400-normal.woff2')})}
html,body{margin:0;background:#fbfaf6}svg{display:block}`;
const nombres = { '1-precio-por-dia.svg': 'precios-pesca-guiada-por-pescador-y-dia.png', '2-fichas-con-precio.svg': 'precios-pesca-fichas-con-precio.png' };
const b = await chromium.launch();
const p = await b.newPage({ viewport: { width: 1200, height: 900 }, deviceScaleFactor: 4 / 3 });
for (const [f, n] of Object.entries(nombres)) {
  await p.setContent(`<!doctype html><meta charset="utf-8"><style>${css}</style>${readFileSync(join(aqui, f), 'utf8')}`);
  await p.evaluate(() => document.fonts.ready);
  const out = join(sal, n);
  writeFileSync(out, await p.screenshot({ type: 'png', clip: { x: 0, y: 0, width: 1200, height: 900 } }));
  spawnSync('python3', ['-I', '-c', 'import sys;from PIL import Image;i=Image.open(sys.argv[1]).convert("RGB").quantize(96,method=Image.Quantize.MEDIANCUT,dither=Image.Dither.NONE);i.save(sys.argv[1],optimize=True)', out], { stdio: 'inherit' });
  console.log('png', out);
}
await b.close();
