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
	composer exec --verbose phpunit tests

test-coverage:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml

test-coverage-text:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-text

db-prepare:
#	psql -a -d $(DATABASE_URL) -f database.sql
	php db-prepare.php
