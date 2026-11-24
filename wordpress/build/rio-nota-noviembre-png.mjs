// SVG -> PNG for the November note infographics (21/11/2026). node wordpress/build/rio-nota-noviembre-png.mjs [carpeta-svg] [carpeta-png] [--chequeo]
// Chromium (playwright) with the brand fonts of the theme embedded; 1200 x 900 viewBox at 4/3 = 1600 x 1200 px.
// CHROME_PATH=/usr/bin/google-chrome if playwright's own browser is not installed. --chequeo also reports text that leaves the
// canvas or overlaps another text (bounding boxes), and writes a 390 px wide copy next to each PNG (name-390.png, not for upload).
import { readFileSync, writeFileSync, mkdirSync, readdirSync } from 'node:fs';
import { createRequire } from 'node:module';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { spawnSync } from 'node:child_process';
const require = createRequire(import.meta.url);
const { chromium } = require('playwright');
const aqui = dirname(fileURLToPath(import.meta.url));
const fuentes = resolve(aqui, '../themes/pescamapa/assets/fonts');
const args = process.argv.slice(2).filter((a) => !a.startsWith('--'));
const chequeo = process.argv.includes('--chequeo');
const ent = resolve(args[0] || join(aqui, 'rio-nota-noviembre'));
const sal = resolve(args[1] || join(aqui, '../fotos'));
mkdirSync(sal, { recursive: true });
const b64 = (f) => readFileSync(join(fuentes, f)).toString('base64');
const css = `
@font-face{font-family:Outfit;font-weight:100 900;src:url(data:font/woff2;base64,${b64('outfit-latin-wght-normal.woff2')})}
@font-face{font-family:'Playfair Display';font-weight:400 900;src:url(data:font/woff2;base64,${b64('playfair-display-latin-wght-normal.woff2')})}
@font-face{font-family:'Bebas Neue';font-weight:400;src:url(data:font/woff2;base64,${b64('bebas-neue-latin-400-normal.woff2')})}
html,body{margin:0;background:#fbfaf6}svg{display:block}`;
const nombres = {
  '1-nunca-en-noviembre.svg': 'parana-goya-nunca-en-noviembre.png',
  '2-el-rio-dia-por-dia.svg': 'parana-goya-el-rio-dia-por-dia-24-nov.png',
  '3-la-misma-agua-rio-abajo.svg': 'parana-la-misma-agua-rio-abajo.png',
};
const b = await chromium.launch(process.env.CHROME_PATH ? { executablePath: process.env.CHROME_PATH } : {});
const p = await b.newPage({ viewport: { width: 1200, height: 900 }, deviceScaleFactor: 4 / 3 });
for (const f of readdirSync(ent).filter((x) => x.endsWith('.svg') && nombres[x]).sort()) {
  const svg = readFileSync(join(ent, f), 'utf8');
  await p.setContent(`<!doctype html><meta charset="utf-8"><style>${css}</style>${svg}`);
  await p.evaluate(() => document.fonts.ready);
  if (chequeo) {
    const r = await p.evaluate(() => {
      const t = [...document.querySelectorAll('svg text')].map((e) => { const q = e.getBBox(); return { s: e.textContent.slice(0, 40), x0: q.x, y0: q.y + q.height * 0.18, x1: q.x + q.width, y1: q.y + q.height * 0.88 }; });
      const sale = t.filter((e) => e.x0 < 8 || e.x1 > 1192 || e.y1 > 898).map((e) => `FUERA: ${e.s} (${e.x0 | 0}-${e.x1 | 0}, ${e.y1 | 0})`);
      const choca = [];
      for (let i = 0; i < t.length; i++) for (let j = i + 1; j < t.length; j++) {
        const a = t[i], c = t[j];
        if (a.x0 < c.x1 - 2 && c.x0 < a.x1 - 2 && a.y0 < c.y1 - 2 && c.y0 < a.y1 - 2) choca.push(`CHOCAN: «${a.s}» con «${c.s}»`);
      }
      return sale.concat(choca);
    });
    console.log(f, r.length ? '\n  ' + r.join('\n  ') : 'sin textos fuera ni superpuestos');
  }
  const out = join(sal, nombres[f]);
  const buf = await p.screenshot({ type: 'png', clip: { x: 0, y: 0, width: 1200, height: 900 } });
  writeFileSync(out, buf);
  if (chequeo) {
    const p2 = await b.newPage({ viewport: { width: 390, height: 293 }, deviceScaleFactor: 1 });
    await p2.setContent(`<!doctype html><style>html,body{margin:0}img{width:390px;display:block}</style><img src="data:image/png;base64,${buf.toString('base64')}">`);
    writeFileSync(out.replace(/\.png$/, '-390.png'), await p2.screenshot({ type: 'png', clip: { x: 0, y: 0, width: 390, height: 293 } }));
    await p2.close();
  }
  // flat colours: a 96-colour palette keeps the text sharp and each PNG small (Pillow)
  spawnSync('python3', ['-I', '-c', 'import sys;from PIL import Image;i=Image.open(sys.argv[1]).convert("RGB").quantize(96,method=Image.Quantize.MEDIANCUT,dither=Image.Dither.NONE);i.save(sys.argv[1],optimize=True)', out], { stdio: 'inherit' });
  console.log('png', out);
}
await b.close();
