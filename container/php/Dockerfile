FROM alpine:3.16 as base

RUN apk --no-cache add \
    php81 \
    php81-ctype \
    php81-curl \
    php81-fileinfo \
    php81-iconv \
    php81-json \
    php81-mbstring \
    php81-dom \
    php81-xml \
    php81-xmlwriter \
    php81-pdo \
    php81-pgsql \
    php81-pdo_pgsql \
    php81-openssl \
    php81-phar \
    php81-tokenizer \
    php81-zlib \
    php81-zip \
    php81-opcache \
    php81-fpm \
    supervisor

RUN mkdir -p /var/log/supervisor
RUN mkdir -p /var/run/php/
RUN mkdir -p /var/run/php-fpm81/
RUN mkdir -p /var/run/php81/

ADD php-supervisor.ini /etc/supervisor.d/php-supervisor.ini

RUN ln -s /usr/bin/php81 /usr/sbin/php  \
    && ln -s /usr/sbin/php-fpm81 /usr/sbin/php-fpm

RUN rm -rf /etc/php81/php-fpm.d/
COPY ./php-fpm.conf /etc/php81/

WORKDIR /app

RUN adduser -D -H -u 1000 -s /bin/bash www-data -G www-data

RUN chown -R www-data:www-data /app

EXPOSE 9000

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

CMD ["/usr/bin/supervisord", "-n"]
