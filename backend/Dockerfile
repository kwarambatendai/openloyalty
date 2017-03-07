FROM php:7.0-apache

RUN apt-get update \
    && apt-get install -y locales libicu-dev zlib1g-dev libghc-postgresql-libpq-dev git phpunit libcurl4-openssl-dev \
    && locale-gen C.UTF-8 \
    && /usr/sbin/update-locale LANG=C.UTF-8 \
    && apt-get autoremove -y \
    && apt-get clean all

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/include/postgresql/ \
    && docker-php-ext-install pdo pgsql pdo_pgsql pdo_mysql mysqli intl opcache bcmath zip curl
RUN docker-php-ext-enable curl
RUN usermod --non-unique --uid 1000 www-data

RUN pecl install xdebug && docker-php-ext-enable xdebug

ADD https://www.phing.info/get/phing-latest.phar /usr/local/bin/phing
COPY docker/vhost.conf /etc/apache2/sites-enabled/000-default.conf
COPY docker/php.ini /usr/local/etc/php/php.ini

RUN a2enmod rewrite headers
RUN chmod +x /usr/local/bin/phing

RUN mkdir -p /var/www/ol/backend
COPY . /var/www/ol/backend
ADD docker/rancher/entrypoint.sh /rancher_entrypoint.sh
RUN chmod +x /rancher_entrypoint.sh
ADD docker/rancher/prod_entrypoint.sh /rancher_prod_entrypoint.sh
RUN chmod +x /rancher_prod_entrypoint.sh
ADD docker/rancher/test_entrypoint.sh /rancher_test_entrypoint.sh
RUN chmod +x /rancher_test_entrypoint.sh
WORKDIR /var/www/ol/backend
RUN chmod -R 777 /var/www/ol/backend/var && \
 chown -R www-data:www-data /var/www/ol/backend/app/uploads && \
 chmod -R 777 /var/www/ol/backend/app/uploads && \
 chown -R www-data:www-data /var/www/ol/backend/app/Resources/frontend_translations && \
 chmod -R 777 /var/www/ol/backend/app/Resources/frontend_translations

RUN cp /var/www/ol/backend/app/config/parameters.yml.dist /var/www/ol/backend/app/config/parameters.yml
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

CMD composer install && bin/console assets:install --symlink && chown -R www-data:www-data /var/www/ol/backend/var && chown -R www-data:www-data /var/www/ol/backend/app/uploads && apache2-foreground
