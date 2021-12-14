#! /bin/bash

export _UID="$(id -u)" \
    && export _GID="$(id -g)" \
    && time docker-compose run --rm --no-deps --user="${_UID}:${_GID}" composer \
    && time docker-compose run --rm --no-deps --user="${_UID}:${_GID}" composer global require "fxp/composer-asset-plugin:^1.3.1" \
    && time docker-compose run --rm --no-deps --user="${_UID}:${_GID}" composer install \
    && time docker-compose run --rm --user="${_UID}:${_GID}" php yii migrate \
    && docker-compose up --remove-orphans nginx