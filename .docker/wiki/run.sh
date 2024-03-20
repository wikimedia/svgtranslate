#!/bin/bash

if [[ ! -f "/var/www/html/images/my_wiki.sqlite" ]]; then
    echo "No database file found. Installing Mediawiki now."
    rm LocalSettings.php
    php /var/www/html/maintenance/run install --dbtype sqlite --pass admin123admin --dbpath /var/www/html/images svgtranslate admin
fi
echo "Copying ./docker/wiki/LocalSettings.php to be used."
cp LocalSettings_tmp.php LocalSettings.php

## Run update, to install the OAuth extension.
php /var/www/html/maintenance/run update --quick

## Start Apache (because this run.sh script overrides the grantparent Docker image's CMD).
apache2-foreground
