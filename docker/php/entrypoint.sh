#!/bin/bash
set -e

# ═══════════════════════════════════════════════════════════════════════════════
# MonkeysLegion v2 — PHP Container Entrypoint
# ═══════════════════════════════════════════════════════════════════════════════

WORKDIR="/var/www/html"

echo "🚀 MonkeysLegion PHP container starting..."

# ── Ensure runtime directories exist and are writable ─────────────────────────
mkdir -p \
    "$WORKDIR/var/cache" \
    "$WORKDIR/var/cache/views" \
    "$WORKDIR/var/cache/phpstan" \
    "$WORKDIR/var/log" \
    "$WORKDIR/var/sessions" \
    "$WORKDIR/var/migrations" \
    "$WORKDIR/storage/cache" \
    "$WORKDIR/storage/logs" \
    "$WORKDIR/public/build"

# Fix ownership for PHP-FPM worker (www-data) — needed for bind mounts
chown -R www-data:www-data \
    "$WORKDIR/var" \
    "$WORKDIR/storage" \
    "$WORKDIR/public/build"
chmod -R 775 \
    "$WORKDIR/var" \
    "$WORKDIR/storage" \
    "$WORKDIR/public/build"

# ── Install Composer dependencies if missing ─────────────────────────────────
if [ ! -d "$WORKDIR/vendor" ] || [ ! -f "$WORKDIR/vendor/autoload.php" ]; then
    echo "📦 Installing Composer dependencies..."
    cd "$WORKDIR"
docker compose exec app composer update monkeyscloud/monkeyslegion --no-interaction
else
    echo "✅ Composer dependencies already present."
fi

# ── Generate APP_KEY if missing ───────────────────────────────────────────────
if [ ! -f "$WORKDIR/.env" ]; then
    echo "⚠️  No .env file found — copying from .env.example"
    cp "$WORKDIR/.env.example" "$WORKDIR/.env"
fi

# Check if APP_KEY is set (not empty)
APP_KEY=$(grep -E '^APP_KEY=' "$WORKDIR/.env" | cut -d'=' -f2- | tr -d '"' || true)
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "🔑 Generating APP_KEY..."
    cd "$WORKDIR" && php bin/ml key:generate --force || true
fi

echo "✅ Setup complete. Handing off to: $@"
exec "$@"
