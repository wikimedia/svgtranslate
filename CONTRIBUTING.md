Contributing
============

## Report bugs

Please report all bugs and feature requests
via the Wikimedia Phabricator
[#SVG Translate Tool](https://phabricator.wikimedia.org/tag/svg_translate_tool/)
project.

## Install for development

All methods of installation require the registration of an OAuth consumer
on the wiki to which you will upload SVGs (probably a local development wiki).
Do this via e.g. `http://localhost/wiki/Special:OAuthConsumerRegistration/propose`
and make sure you include the following grants:

* Create, edit, and move pages
* Upload new files
* Upload, replace, and move files

The `OAUTH_URL` environment variable must be set to the long form of the URL
such as `http://localhost/w/index.php?title=Special:OAuth`.

To configure the wiki that's used to fetch images and for upload
you should also set `WIKI_URL` environment variable
to the API URL of your development wiki, e.g. `http://localhost/w/api.php`.

### Install using Docker

Prerequisites: 

* [Docker](https://www.docker.com/)
* [Docker Compose](https://docs.docker.com/compose/install/)

Install code and start development environment:

```
git clone https://github.com/wikimedia/svgtranslate
cd svgtranslate
cp .env.dist .env
docker-compose up
```

Now you should be able to view the tool at [http://localhost:8042/](http://localhost:8042/).
A wiki is also available at [http://localhost:8043/](http://localhost:8043/),
to which you'll need to upload an SVG file or two.
Log in as `Admin` with `admin123`.

To work with uploading, you'll need to register a new OAuth consumer on that wiki,
and add its key and secret to your `.env` file.
The data for the wiki is kept in the `var/wiki/` directory (including the Sqlite database)
so it will persist when Docker is not running.

The usual commands you'll need for development are as follows:

1. If you change anything in `assets/` you'll need to rebuild the asset files
   (running `npm install` before the first time doing this, if you haven't already):

       docker-compose exec assets npm run build

   Or, to have it monitor the files for changes while you're working
   (note the double-hyphens to to prevent the `watch` flag from being treated as an argument to `npm`):

       docker-compose exec assets npm run watch

2. If you change `composer.json` or `packages.json`, you need to update:

       docker-compose exec web composer update
       docker-compose exec assets npm update

3. To run tests:

       docker-compose exec web composer test
       docker-compose exec assets npm run test

You can get to a shell inside any of the containers with the following:

    docker-compose exec <service_name> bash

### Install manually

Prerequisites:

* [PHP](https://www.php.net/)
* [Composer](https://getcomposer.org/)
* [npm](https://www.npmjs.com/)

Install code and dependencies:

```
git clone https://github.com/wikimedia/svgtranslate
cd svgtranslate
composer install
npm install
```

Run development web server:

    ./bin/console server:run

## Development commands

Run tests:

    composer test
    npm run test

Generate assets:

    npm run build

## Deployment on Toolforge

The tool is deployed in a `tool/` directory in the tool's home directory,
with `tool/public/` symlinked from `public_html/`.

To deploy, first checkout the relevant version into `tool/`,
and then load the PHP and Node installation jobs from the home directory:

```console
$ toolforge-jobs load tool/toolforge/install.yaml
```
