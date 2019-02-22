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

Now you should be able to view the tool at [http://localhost:8042/](http://localhost:8042/)
If you need to change the port, edit `TOOLFORGE_DOCKER_PORT` in your `svgtranslate/.env` file.

To run Composer commands on the server, connect to the container:

    docker exec -it -u dev svgtranslate_web bash

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

Run linting:

    composer lint
    npm run lint

Run tests:

    composer test
    npm run test

Generate assets:

    ./node_modules/.bin/encore production

Note that the generated assets (in `public/assets/`) are also committed to Git,
for ease of deployment in production (where we don't have Node).
