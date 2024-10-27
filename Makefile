PORT ?= 8000
#DATABASE_URL ?= `sed -n '/^DATABASE_URL/s/.*=//p' .env`

start:
	PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:$(PORT) -t public

install:
	composer install --no-dev

install-dev:
	composer install

lint:
	composer exec --verbose phpcs -- --standard=PSR12 public tests app bootstrap public
	composer exec --verbose phpstan

lint-fix:
	composer exec --verbose phpcbf -- --standard=PSR12 public tests app bootstrap public

validate:
	composer validate

test:
	cp tests/Test.php.txt tests/Test.php
	composer exec --verbose phpunit tests
	rm -f tests/Test.php

test-coverage:
	cp tests/Test.php.txt tests/Test.php
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml
	rm -f tests/Test.php

test-coverage-text:
	cp tests/Test.php.txt tests/Test.php
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-text
	rm -f tests/Test.php

db-prepare:
#	psql -a -d $(DATABASE_URL) -f database.sql
	php db-prepare.php
