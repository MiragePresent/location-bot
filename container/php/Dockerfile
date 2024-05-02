FROM alpine:3.19 as base

RUN apk --no-cache add \
    php \
    php-ctype \
    php-curl \
    php-fileinfo \
    php-iconv \
    php-json \
    php-mbstring \
    php-dom \
    php-xml \
    php-xmlwriter \
    php-pgsql \
    php-pdo \
    php-pdo_pgsql \
    php-openssl \
    php-phar \
    php-tokenizer \
    php-zlib \
    php-zip \
    php-opcache \
    php-fpm \
    supervisor

RUN mkdir -p /var/log/supervisor
RUN mkdir -p /var/run/php/
RUN mkdir -p /var/run/php-fpm82/
RUN mkdir -p /var/run/php82/

ADD php-supervisor.ini /etc/supervisor.d/php-supervisor.ini

RUN ln -s /usr/sbin/php-fpm82 /usr/sbin/php-fpm

RUN rm -rf /etc/php82/php-fpm.d/
COPY ./php-fpm.conf /etc/php82/

WORKDIR /app

RUN adduser -D -H -u 1000 -s /bin/bash www-data -G www-data

RUN chown -R www-data:www-data /app

EXPOSE 9000

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

CMD ["/usr/bin/supervisord", "-n"]
