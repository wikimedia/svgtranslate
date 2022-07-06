#!/bin/bash

# Give the web server access to write cache and log files.
chown -R www-data:www-data var

# Install dependencies.
composer install

# Start the Lighttpd web server.
echo SVG Translate is now at http://localhost:8042
lighttpd -D -f /etc/lighttpd/lighttpd.conf || exit 1;
