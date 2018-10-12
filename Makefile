.PHONY: install test

.DEFAULT: install

install:
	composer install

test:
	composer test
	composer lint
