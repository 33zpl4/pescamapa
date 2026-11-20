#!/usr/bin/env bash
# Chequeo local y gratis (17/11/2026): reemplaza a Test WordPress como primer filtro antes de pushear.
#   bash wordpress/build/chequeo-local.sh
# No instala WordPress: no prueba renderizado ni el formulario (eso es Test WordPress, solo por dispatch,
# para cambios riesgosos de plugin o tema). Atrapa lo barato: errores de sintaxis PHP, reglas de marca, YAML.
set -u
cd "$(dirname "$0")/../.."
fallo=0

echo "· php -l sobre wordpress/**/*.php"
n=0
while IFS= read -r f; do
	n=$((n + 1))
	out=$(php -l "$f" 2>&1) || { echo "✖ $out"; fallo=1; }
done < <(find wordpress -name '*.php' -not -path '*/node_modules/*' | sort)
echo "  $n archivos PHP revisados"

echo "· Reglas de marca (docs/marca.md)"
node wordpress/build/revisar-marca.mjs || fallo=1

echo "· node --check sobre wordpress/build/*.mjs"
for f in wordpress/build/*.mjs; do node --check "$f" || fallo=1; done

echo "· Autoprueba del histórico del río (offline)"
TMPDIR="${TMPDIR:-/tmp}" python3 -I wordpress/build/rio-historia.py --autotest || fallo=1

echo "· Workflows: YAML válido"
if command -v python3 >/dev/null && python3 -c 'import yaml' 2>/dev/null; then
	for f in .github/workflows/*.yml; do
		python3 -c 'import sys,yaml; yaml.safe_load(open(sys.argv[1]))' "$f" 2>/dev/null || { echo "✖ YAML inválido: $f"; fallo=1; }
	done
else
	echo "  (sin python3/yaml: se omite)"
fi

echo "· Marcas PENDIENTE sueltas en plantillas del tema"
if grep -rn --include='*.php' 'PENDIENTE' wordpress/themes >/dev/null 2>&1; then
	grep -rn --include='*.php' 'PENDIENTE' wordpress/themes | head -5
	echo "  (aviso: revisar que sean lógica de detección y no texto visible)"
fi

if [ "$fallo" -eq 0 ]; then echo "✓ Chequeo local: todo en orden."; else echo "✖ Chequeo local: hay errores arriba."; fi
exit "$fallo"
