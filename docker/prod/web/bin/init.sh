#!/usr/bin/env bash

if ! [ -z "$BACKEND_API_URL" ]; then
    sed -i -e 's/APIURL/'"${BACKEND_API_URL}"'/g' /var/www/openloyalty/front/assets/js/commons.js
fi
