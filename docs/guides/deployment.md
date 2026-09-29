# Deployment

## Production Environment

### Requirements

- PHP 8.4+
- Composer 2.0+
- Nginx or Apache
- PHP-FPM
- MySQL 8.0+ / PostgreSQL 16+ / SQLite 3.35+
- Redis (recommended for cache, queue, sessions)

### Environment Configuration

Set production environment variables in `.env`:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your-generated-key
APP_URL=https://your-domain.com

DB_HOST=127.0.0.1
DB_DATABASE=your_database
DB_USERNAME=your_user
DB_PASSWORD=your_password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_DRIVER=redis
```

**Never commit `.env` to version control.**

### OPcache Preload

Generate an OPcache preload script for faster startup:

```bash
php bin/ml opcache:preload
php bin/ml opcache:status
```

Add to `php.ini`:

```ini
opcache.preload=/path/to/var/preload.php
opcache.preload_user=www-data
```

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/my-app/public;
    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] `APP_KEY` set and kept secret
- [ ] HTTPS enforced (redirect HTTP → HTTPS)
- [ ] `config/security.mlc` CSP headers configured
- [ ] Rate limiting enabled (`#[Throttle]` on sensitive routes)
- [ ] CSRF protection on POST routes
- [ ] Database credentials not in version control
- [ ] `storage/` and `var/` directories writable by web server
- [ ] `public/` is the only web-accessible directory

## Docker Deployment

### Dockerfile

```dockerfile
FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    libzip-dev libpng-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip gd opcache

COPY . /var/www/html
WORKDIR /var/www/html

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data storage var
```

### Docker Compose

```yaml
services:
  app:
    build: .
    volumes:
      - ./:/var/www/html
    depends_on:
      - db
      - redis

  db:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: monkeyslegion
      MYSQL_ROOT_PASSWORD: secret

  redis:
    image: redis:7-alpine

  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
    volumes:
      - ./docker/nginx.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - app
```

## CI/CD with GitHub Actions

```yaml
name: Deploy
on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
      - run: composer install --no-dev --optimize-autoloader
      - run: php bin/ml test
      - run: php bin/ml migrate --force
      # Deploy via SSH, rsync, or Docker push
```

## Optimization Commands

```bash
# Compile the DI container for faster resolution
php bin/ml container:compile

# Cache configuration
php bin/ml config:cache

# Cache routes
php bin/ml route:cache

# Cache templates
php bin/ml view:cache

# Run all optimizations
php bin/ml optimize
```

## Zero-Downtime Deployment

1. Deploy new code to a new release directory
2. Run `php bin/ml migrate --force` (non-destructive migrations only)
3. Symlink switch: `current → releases/new`
4. Reload PHP-FPM: `service php8.4-fpm reload`
5. Run `php bin/ml optimize` in the new release
