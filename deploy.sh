#!/usr/bin/env bash
# Rebuild and redeploy Drop N Share after a `git pull`.
# Run from the project root:  ./deploy.sh
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
WEB_USER="www"

echo "==> Backend: composer dependencies"
cd "$ROOT/backend"
composer install --no-dev --no-interaction --optimize-autoloader

echo "==> Backend: migrations"
php artisan migrate --force

echo "==> Backend: storage link"
php artisan storage:link || true

echo "==> Backend: clearing caches"
# Config is intentionally NOT cached: caching it makes .env edits (such as
# switching SQLite -> MySQL) silently take no effect until you clear it again.
php artisan config:clear
php artisan cache:clear || true
php artisan view:clear

echo "==> Frontend: npm dependencies + production build"
cd "$ROOT/frontend"
npm ci --no-audit --no-fund
npm run build

echo "==> Permissions"
chown -R "$WEB_USER:$WEB_USER" "$ROOT"
find "$ROOT/backend/storage" -type d -exec chmod 775 {} \;
find "$ROOT/backend/storage" -type f -exec chmod 664 {} \;
chmod -R 775 "$ROOT/backend/bootstrap/cache"
chmod 775 "$ROOT/backend/database"
[ -f "$ROOT/backend/database/database.sqlite" ] && chmod 664 "$ROOT/backend/database/database.sqlite"
chmod 640 "$ROOT/backend/.env"

echo "==> Done."
echo "    Frontend: $ROOT/frontend/dist"
echo "    Backend : $ROOT/backend/public"
