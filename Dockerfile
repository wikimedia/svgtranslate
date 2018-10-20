FROM docker-registry.tools.wmflabs.org/toollabs-php72-web

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN groupadd dev -g 999
RUN useradd dev -g dev -d /home/dev -m

WORKDIR /var/www/

CMD ["./bin/dockerstart"]
