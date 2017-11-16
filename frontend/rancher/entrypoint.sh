#!/usr/bin/env bash

sed -i -e 's@"http://openloyalty.localhost/api"@'\"${API_HOST}\"'@g' /var/www/openloyalty/front/config.js
nginx -g 'daemon off;'
