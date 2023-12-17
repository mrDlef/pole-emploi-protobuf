install:
	composer install
	bin/console protobuf:install

compile: install
	bin/console download
	bin/console generate:protobuf
	bin/console protobuf:compile

