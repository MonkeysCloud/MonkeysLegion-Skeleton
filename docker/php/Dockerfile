###############################################################################
# MonkeysLegion v2 — PHP-FPM 8.4 Development Image
###############################################################################

FROM php:8.4-fpm-bookworm

# ── System dependencies ──────────────────────────────────────────────────────
RUN apt-get update && apt-get install -y --no-install-recommends \
        libpq-dev \
        libicu-dev \
        libzip-dev \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        libcurl4-openssl-dev \
        git \
        unzip \
        procps \
        curl \
        ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# ── PHP extensions (docker-php-ext-install) ──────────────────────────────────
RUN docker-php-ext-install -j"$(nproc)" \
        pdo_pgsql \
        pgsql \
        intl \
        mbstring \
        zip \
        bcmath \
        gd \
        pcntl \
        sockets \
        opcache \
        exif

# ── PECL extensions ──────────────────────────────────────────────────────────
RUN pecl install redis && docker-php-ext-enable redis

# ── Composer ─────────────────────────────────────────────────────────────────
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ── Configuration ────────────────────────────────────────────────────────────
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-monkeyslegion.ini

# ── Working directory ────────────────────────────────────────────────────────
WORKDIR /var/www/html

# ── Entry point ──────────────────────────────────────────────────────────────
COPY docker/php/entrypoint.sh /usr/local/bin/ml-entrypoint
RUN chmod +x /usr/local/bin/ml-entrypoint

ENTRYPOINT ["ml-entrypoint"]
CMD ["php-fpm"]
