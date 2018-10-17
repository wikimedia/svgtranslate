SVG Translate
=============

* Project page: https://meta.wikimedia.org/wiki/Community_Tech/SVG_translation
* Issue tracker: https://phabricator.wikimedia.org/tag/svg_translate_tool/
* Source code: https://github.com/wikimedia/svgtranslate

[![Build Status](https://travis-ci.org/wikimedia/svgtranslate.svg)](https://travis-ci.org/wikimedia/svgtranslate)

For information about contributing to this project, see [CONTRIBUTING.md](CONTRIBUTING.md).

## Development

To create a development environment and contribute:
* Clone the repository
* In the repository, run `docker-compose up -d`
* Visit `http://localhost:8042` in the browser

If you need to run composer (install or update), you can do that through the docker image, by running:
* To run the php bash: `docker exec -it -u dev svgtranslate_sf4_php bash`
* And then run `composer install`
