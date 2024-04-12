LOCAL_USER  := $(shell id -u)
COMPOSER 	:= docker compose run --rm -u ${LOCAL_USER} composer
PHP 	    := docker compose run --rm -u ${LOCAL_USER} php


default:
	@cat Make/commands.txt

install:
	${COMPOSER} install
	make _fix_group

update:
	${COMPOSER} update
	make _fix_group

composer:
	${COMPOSER} bash

bash:
	${PHP} bash

run-tests:
	${PHP} bin/phpunit

### Internal
_fix_group:
	chown :$(shell id -g) * -R
