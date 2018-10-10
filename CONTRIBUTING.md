Contributing
============

## Report bugs

Please report all bugs and feature requests
via the Wikimedia Phabricator
[#SVG Translate Tool](https://phabricator.wikimedia.org/tag/svg_translate_tool/)
project.

## Install for development

Prerequesites:
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

Run linting:

    composer lint

Run tests:

    composer test

Run development web server:

    ./bin/console server:run

Generate assets:

    ./node_modules/.bin/encore production --watch
