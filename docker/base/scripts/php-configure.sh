#!/usr/bin/env bash

apt-get -qy update \
    && apt-get install -qy locales libicu-dev zlib1g-dev libghc-postgresql-libpq-dev git libcurl4-openssl-dev vim netcat postgresql python-setuptools \
    && locale-gen C.UTF-8 \
    && /usr/sbin/update-locale LANG=C.UTF-8 \
    && apt-get autoremove -y \
    && apt-get clean all

docker-php-ext-configure pgsql -with-pgsql=/usr/include/postgresql/ && docker-php-ext-install pdo pgsql pdo_pgsql pdo_mysql mysqli intl opcache bcmath zip curl
docker-php-ext-enable curl