#!/usr/bin/env bash
function info {
    printf "\033[0;36m===> \033[0;33m${1}\033[0m\n"
}

until nc -z ${APP_DB_HOST} ${APP_DB_PORT}; do
    info "Waiting database on ${APP_DB_HOST}:${APP_DB_PORT} ..."
    sleep 2
done

until nc -z ${ELK_HOST} 9200; do
    info "Waiting for elasticsearch"
    sleep 2
done

info "Setting permissions."
chown -R www-data:www-data /var/www/openloyalty/var

info "Checking if database is already initialized"
if [[ `PGPASSWORD="$APP_DB_PASSWORD" psql -h $APP_DB_HOST -U $APP_DB_USER -l -A -t | grep $APP_DB_NAME | wc -l` -gt 0 ]] \
    && ! [[ `PGPASSWORD="$APP_DB_PASSWORD" psql -h $APP_DB_HOST -U $APP_DB_USER -d $APP_DB_NAME -A -t -q -c '\dt' | wc -l` -gt 0 ]]; then
      info "Running initializing scripts..."
      runuser -s /bin/sh -c 'phing setup' www-data
      info "Tables are initialized."
else
    info "Database already initialized."
fi

info "Starting supervisord with php-fpm and cron."
exec /usr/local/bin/supervisord -n -c /etc/supervisord/supervisord.conf
