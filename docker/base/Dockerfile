ARG CACHE_FULL

FROM ${CACHE_FULL}/composer:2.8.8 as compose
FROM ${CACHE_FULL}/php:8.2-fpm-alpine3.19

COPY --from=compose /usr/bin/composer /usr/bin/composer

ARG PHP_EXTENSIONS="intl pcntl pdo_pgsql sockets opcache"
ARG PECL_EXTENSIONS="sync amqp redis"
ENV APP_USER petproject

RUN  apk add --update && apk add -U --no-cache \
     ${PHPIZE_DEPS} \
     rabbitmq-c-dev \
     icu-dev \
     libpq-dev \
     linux-headers \
     gettext \
     unzip \
     openssl \
     curl-dev \
     jq && \
     pecl install ${PECL_EXTENSIONS} && \
     docker-php-ext-install ${PHP_EXTENSIONS} && \
     docker-php-ext-enable ${PECL_EXTENSIONS} && \
     addgroup -g 23000 ${APP_USER} && adduser -u 23000 -G ${APP_USER} -D -s /bin/sh ${APP_USER} && adduser ${APP_USER} ${APP_USER} && \
     apk del ${PHPIZE_DEPS} && \
     rm -rf /usr/local/etc/php-fpm.d/*

# права на папку нужны для работы code coverage в phpstorm (он туда складывает отчёты)
RUN chown -R ${APP_USER}:${APP_USER} /opt && \
    chmod 777 /opt
