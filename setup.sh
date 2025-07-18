#!/bin/bash

set -e 

docker run --rm \
  -u "$(id -u):$(id -g)" \
  -v $(pwd):/var/www/html \
  -w /var/www/html \
  laravelsail/php84-composer:latest \
  composer install

cp .env.example .env
