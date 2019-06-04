#!/bin/sh

if [[ ! -f "/var/www/html/images/my_wiki.sqlite" ]]; then
    php /var/www/html/maintenance/install.php --dbtype sqlite --pass admin123 --dbpath /var/www/html/images svgtranslate admin
fi
cp LocalSettings_tmp.php LocalSettings.php

## Run update, to install the OAuth extension.
php maintenance/update.php

## Start Apache (because this run.sh script overrides the grantparent Docker image's CMD).
apache2-foreground
