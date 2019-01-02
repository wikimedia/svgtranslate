FROM docker-registry.tools.wmflabs.org/toollabs-php72-web

WORKDIR /var/www/

RUN apt-get update && apt-get install -y librsvg2-bin php-xdebug

COPY .docker/web/lighttpd.conf /etc/lighttpd/lighttpd.conf

CMD ".docker/web/run.sh"
