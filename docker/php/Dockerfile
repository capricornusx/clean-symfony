ARG CI \
    IMAGE_API_BASE \
    IMAGE_API_BASE_TAG

FROM ${CI}/${IMAGE_API_BASE}:${IMAGE_API_BASE_TAG} as api

COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php/php.ini /usr/local/etc/php/php.ini

USER ${APP_USER}

WORKDIR /app

COPY --chown=${APP_USER}:${APP_USER} . .

RUN chmod u+x docker/php/data-generation.sh && \
    composer install --no-progress --no-scripts && \
    composer dump-autoload --optimize
