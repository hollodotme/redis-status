composer-php71:
	docker-compose run --rm php71 composer update -o -v
.PHONY: composer-php71

composer-php72:
	docker-compose run --rm php72 composer update -o -v
.PHONY: composer-php72

test-php-71: composer-php71 start-redis
	docker-compose run --rm php71 \
	php -d error_reporting=-1 \
	-d auto_prepend_file=/repo/build/xdebug-filter.php \
	/usr/bin/phpunit \
	-c /repo/build/phpunit7.xml \
	--testdox
.PHONY: test-php-71

test-php-72: composer-php72 start-redis
	docker-compose run --rm php72 \
	php -d error_reporting=-1 \
	/usr/bin/phpunit \
	-c /repo/build/phpunit8.xml \
	--testdox
.PHONY: test-php-72

start-redis:
	docker-compose up -d redis
.PHONY: start-redis