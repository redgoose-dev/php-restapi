#!/bin/bash

case "$1" in
  page)
    php -S 0.0.0.0:8000 -t ./tests/page
    ;;
  api)
    php -S 0.0.0.0:9001 -t ./tests/api
    ;;

  *)
    echo "Usage: ./action.sh {page|api}" >&2
    exit 3
    ;;
esac
