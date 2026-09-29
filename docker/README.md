# MonkeysLegion v2 — Docker Development Environment

A complete Docker setup for local development and testing of the MonkeysLegion platform.

## Prerequisites

- [Docker](https://docs.docker.com/get-docker/) 24+
- [Docker Compose](https://docs.docker.com/compose/install/) v2+

## Quick Start

```bash
# Build and start all services
docker compose up -d --build

# Generate the APP_KEY (first time only)
docker compose exec app php bin/ml key:generate
```

## Service URLs

| Service       | URL                        | Purpose                          |
|---------------|----------------------------|----------------------------------|
| **App**       | http://localhost:8000      | PHP application (nginx → PHP-FPM)|
| **Vite HMR**  | http://localhost:5173      | Vite dev server (hot reload)     |
| **Adminer**   | http://localhost:8080      | PostgreSQL DB management UI      |
| **Mailpit**   | http://localhost:8025      | Email testing inbox              |
| **Meilisearch**| http://localhost:7700     | Search engine dashboard          |
| **PostgreSQL**| localhost:5432             | Direct DB connection             |
| **Redis**     | localhost:6379             | Direct Redis connection          |

## Adminer Connection

Connect to PostgreSQL via Adminer at http://localhost:8080:

| Field   | Value        |
|---------|--------------|
| System  | PostgreSQL   |
| Server  | postgres     |
| Username| ml           |
| Password| ml_secret    |
| Database| ml_skeleton  |

## Common Commands

```bash
# ── Lifecycle ───────────────────────────────────────────────────────────────
docker compose up -d              # Start all services (background)
docker compose down               # Stop all services
docker compose down -v            # Stop + delete all data volumes
docker compose logs -f            # Follow all logs
docker compose logs -f app        # Follow app logs only

# ── PHP / CLI ────────────────────────────────────────────────────────────────
docker compose exec app bash                      # SSH into PHP container
docker compose exec app php bin/ml list            # List CLI commands
docker compose exec app php bin/ml key:generate    # Generate APP_KEY
docker compose exec app composer test              # Run tests
docker compose exec app composer test:unit         # Run unit tests only
docker compose exec app composer phpstan           # Run static analysis

# ── Frontend ─────────────────────────────────────────────────────────────────
docker compose logs -f node                       # Watch Vite dev server logs
docker compose run --rm node-build                # Build production assets
docker compose restart node                       # Restart Vite dev server

# ── Database ─────────────────────────────────────────────────────────────────
docker compose exec postgres psql -U ml -d ml_skeleton   # PostgreSQL CLI
docker compose exec redis redis-cli                      # Redis CLI
```

## Production Asset Build

To test production-style asset serving:

```bash
# 1. Build assets (outputs to public/build/manifest.json)
docker compose run --rm node-build

# 2. Switch the app to production mode
# Edit .env: APP_ENV=production, APP_DEBUG=false

# 3. Restart the app container
docker compose restart app

# 4. Visit http://localhost:8000 — assets served from built manifest
```

To switch back to dev mode, set `APP_ENV=development` in `.env` and restart:
```bash
docker compose restart app
```

## Volumes

Persistent data is stored in named Docker volumes:

| Volume         | Service     | Data                          |
|----------------|-------------|-------------------------------|
| `pg_data`      | postgres    | PostgreSQL database files     |
| `redis_data`   | redis       | Redis append-only file        |
| `meili_data`   | meilisearch | Meilisearch index data        |
| `mailpit_data` | mailpit     | Mailpit stored messages       |

Source code is bind-mounted for hot-reload during development.

## Environment Configuration

The `.env` file is configured for Docker with these key settings:

- `DB_CONNECTION=pgsql`, `DB_HOST=postgres`
- `REDIS_HOST=redis`
- `CACHE_DRIVER=redis`, `SESSION_DRIVER=redis`, `QUEUE_DEFAULT=redis`
- `SEARCH_DRIVER=meilisearch`, `MEILISEARCH_HOST=http://meilisearch:7700`
- `MAIL_DRIVER=smtp`, `MAIL_HOST=mailpit`, `MAIL_PORT=1025`
- `FEATURE_FLAGS_DRIVER=redis`

See `.env.docker` for a full reference of Docker environment variables.

## Troubleshooting

### Port already in use

If a port is already in use on your host, edit `docker-compose.yml` and change the port mapping (e.g. `"8001:80"` instead of `"8000:80"`).

### Composer dependencies not found

The PHP container installs dependencies automatically on first start. To force reinstall:
```bash
docker compose exec app composer install
```

### Vite dev server not connecting

The Vite dev server binds to `0.0.0.0:5173`. Ensure no firewall is blocking port 5173. Check logs:
```bash
docker compose logs node
```

### Permission issues on Linux

If you see permission errors, the PHP container runs as `root` by default. For production, create a dedicated user. For dev, this is typically fine.

### Rebuild from scratch

```bash
docker compose down -v
docker compose build --no-cache
docker compose up -d
```
