FROM docker-registry.tools.wmflabs.org/toolforge-php72-sssd-web

WORKDIR /var/www/

RUN apt-get update && apt-get install -y librsvg2-bin php-xdebug

COPY .docker/web/lighttpd.conf /etc/lighttpd/lighttpd.conf

CMD ".docker/web/run.sh"
