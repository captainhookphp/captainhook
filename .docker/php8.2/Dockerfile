FROM php:8.2.0RC5-alpine

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
RUN install-php-extensions zip

RUN apk add ncurses \
    && apk add gnupg \
    && apk add bash

COPY --from=composer/composer:2-bin /composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /docker
# Workaround to keep container running
CMD ["tail", "-f", "/dev/null"]
