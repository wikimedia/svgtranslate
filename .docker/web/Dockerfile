FROM docker-registry.tools.wmflabs.org/toolforge-php82-sssd-base

WORKDIR /var/www/

RUN apt-get update \
    && apt-get install -y librsvg2-bin lighttpd php-cgi php-xdebug php-curl php-xml php-intl

COPY .docker/web/lighttpd.conf /etc/lighttpd/lighttpd.conf

CMD ".docker/web/run.sh"
