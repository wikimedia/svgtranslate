#!/bin/bash

# Give the web server access to write cache and log files.
#mkdir -p .composer vendor
chown -R www-data:www-data var

# Install dependencies.
#chsh -s /bin/bash www-data
#su www-data -c "composer install"
which composer
composer install

# Start the Lighttpd web server.
echo SVG Translate is now at http://localhost:8042
lighttpd -D -f /etc/lighttpd/lighttpd.conf || exit 1;
