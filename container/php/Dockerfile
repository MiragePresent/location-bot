FROM php:7.3-fpm as base

# Installing dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libfreetype6-dev \
    libzip-dev \
    libpq-dev \
    locales \
    zip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Installing extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring zip exif pcntl bcmath opcache

# Installing composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configuration for supporting cyrillic fonts in bash
RUN echo "set meta-flag on" >> /etc/.inputrc \
    echo "set convert-meta off" >> /etc/.inputrc

WORKDIR /app
RUN chmod -R 775 /app && \
    chown -R www-data:www-data /app

FROM base as dev

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_post=9002" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=trigger" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY "memory-limit.ini" "/usr/local/etc/php/conf.d/memory-limit.ini"
