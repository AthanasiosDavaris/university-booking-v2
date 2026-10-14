FROM php:8.3-fpm

# Accepts UID/GID from docker-compose
ARG UID=1000
ARG GID=1000

RUN usermod -u ${UID} www-data && groupmod -g ${GID} www-data

# Installs system dependencies
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Installs PHP extenions + pcntl (for queues)
RUN docker-php-ext-install pdo_pgsql zip pcntl opcache

# Installs redis extension from pecl
RUN pecl install redis \
    && docker-php-ext-enable redis

# Installs composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy the rest of the application
COPY --chown=www-data:www-data . .