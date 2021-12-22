FROM alpine:3.15.0 as base

RUN apk --no-cache add \
    php8 \
    php8-ctype \
    php8-curl \
    php8-fileinfo \
    php8-iconv \
    php8-json \
    php8-mbstring \
    php8-dom \
    php8-xml \
    php8-xmlwriter \
    php8-pdo \
    php8-mysqli \
    php8-mysqlnd  \
    php8-pdo_mysql \
    php8-openssl \
    php8-phar \
    php8-tokenizer \
    php8-zlib \
    php8-zip \
    php8-opcache \
    php8-fpm \
    supervisor

RUN mkdir -p /var/log/supervisor
RUN mkdir -p /var/run/php/
RUN mkdir -p /var/run/php8-fpm/
RUN mkdir -p /var/run/php-fpm8/
RUN mkdir -p /var/run/php8/

ADD php-supervisor.ini /etc/supervisor.d/php-supervisor.ini

RUN ln -s /usr/bin/php8 /usr/bin/php && \
    ln -s /usr/sbin/php-fpm8 /usr/sbin/php-fpm

RUN rm -rf /etc/php8/php-fpm.d/
COPY ./php-fpm.conf /etc/php8/

WORKDIR /app

RUN adduser -D -H -u 1000 -s /bin/bash www-data -G www-data

RUN chown -R www-data:www-data /app

EXPOSE 9000

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

CMD ["/usr/bin/supervisord", "-n"]
