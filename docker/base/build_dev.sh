#!/usr/bin/env bash

## build base images
docker build -t openloyalty/base-nodejs -f nodejs-dockerfile .
docker build -t openloyalty/base-nginx -f nginx-dockerfile .
docker build -t openloyalty/base-php-fpm -f php-fpm-dockerfile .
