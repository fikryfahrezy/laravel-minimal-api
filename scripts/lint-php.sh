#!/bin/sh

set -eu

if git rev-parse --git-dir >/dev/null 2>&1; then
    files=$(git ls-files --cached --others --exclude-standard -- '*.php' 'artisan')
else
    files=$(find app bootstrap config database routes tests -type f -name '*.php' -print 2>/dev/null)

    if [ -f artisan ]; then
        files=$(printf '%s\n%s' "$files" "artisan")
    fi
fi

if [ -z "$files" ]; then
    echo "No PHP files found to lint."
    exit 0
fi

printf '%s\n' "$files" | while IFS= read -r file; do
    if [ -z "$file" ] || [ ! -f "$file" ]; then
        continue
    fi

    php -l "$file" >/dev/null
done

echo "PHP lint passed."