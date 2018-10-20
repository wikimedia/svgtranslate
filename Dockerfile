FROM docker-registry.tools.wmflabs.org/toollabs-php72-web

ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["./var/www/bin/dockerstart"]
