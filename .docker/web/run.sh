#!/bin/sh

# Check that we're running within docker-compose.
if [ -z $TOOLFORGE_DOCKER_PORT ]; then
    echo "This script is only for use via Docker."
    exit 1
fi

# Make sure the web server can write to the log files.
chown -R www-data:www-data /var/www/var

# Install dependencies.
apt-get update
apt-get install librsvg2-bin
composer install -d /var/www || exit 1;

# Start the Lighttpd web server. 
echo SVG Translate is now at http://localhost:$TOOLFORGE_DOCKER_PORT
lighttpd -D -f /etc/lighttpd/lighttpd.conf || exit 1;
