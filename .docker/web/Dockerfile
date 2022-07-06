FROM docker-registry.wikimedia.org/dev/buster-php74

WORKDIR /var/www/

RUN apt-get update \
    && apt-get install -y librsvg2-bin lighttpd php-cgi php-xdebug php-curl php-xml php-intl \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
    && composer install

COPY .docker/web/lighttpd.conf /etc/lighttpd/lighttpd.conf

CMD ".docker/web/run.sh"
