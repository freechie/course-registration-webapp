#!/usr/bin/env bash
set -euo pipefail

repo_root=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
cd "$repo_root"

while IFS= read -r file; do
    php -l "$file" >/dev/null
done < <(find . -path './.git' -prune -o -name '*.php' -type f -print | sort)

php tests/security_test.php

if [[ "${SKIP_INTEGRATION:-0}" != '1' ]]; then
    ./tests/integration.sh
fi

echo "Verification passed"
