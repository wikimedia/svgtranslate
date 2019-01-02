Contributing
============

## Report bugs

Please report all bugs and feature requests
via the Wikimedia Phabricator
[#SVG Translate Tool](https://phabricator.wikimedia.org/tag/svg_translate_tool/)
project.

## Install for development

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

Asset files will be regenerated whenever you save any changes to any files in `assets/`.

To run commands (e.g. `composer` or `npm`) on the server, connect to one of the containers:

    docker exec -it svgtranslate_web bash
    docker exec -it svgtranslate_assets bash

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

Run tests:

    composer test

Generate assets:

    ./node_modules/.bin/encore production

Note that the generated assets (in `public/assets/`) are also committed to Git,
for ease of deployment in production (where we don't have Node).
