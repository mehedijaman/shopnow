#!/usr/bin/env bash
set -euo pipefail

APP_DIR="$(cd "$(dirname "$0")" && pwd)"
APP_ENV="${APP_ENV:-production}"

echo "==> Deploying to $APP_ENV at $APP_DIR"

cd "$APP_DIR"

echo "==> Pulling latest code..."
git pull --rebase

echo "==> Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Installing Node dependencies and building assets..."
npm ci --no-audit --no-fund
npm run build

echo "==> Caching config, routes, and views..."
php artisan optimize

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Deployment complete."
