#!/usr/bin/env sh

#. .env

( \
  cd "${_SOURCE_ROOT}" \
  && bin/console yii migrate -n \
)