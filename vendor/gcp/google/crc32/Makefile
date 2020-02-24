##
# Copyright 2019 Google Inc. All Rights Reserved.
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#      http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.
##

.PHONY : all clean benchmark test test_all lint ext ext_test

COMPOSER ?= composer
PHP_CS_FIXER ?= vendor/bin/php-cs-fixer
PHP_UNIT ?= vendor/bin/phpunit

PHP_BIN ?= $(shell php-config --prefix)/bin
PHP ?= $(PHP_BIN)/php
PHP_CONFIG ?= $(PHP_BIN)/php-config
PHPIZE ?= $(PHP_BIN)/phpize

all: lint test

clean:
	-rm -r .php_cs.cache
	$(MAKE) -C ext clean

vendor: composer.lock
composer.lock: composer.json
	$(COMPOSER) update
	touch composer.lock

lint: vendor
	$(PHP_CS_FIXER) fix --dry-run --diff src
	$(PHP_CS_FIXER) fix --dry-run --diff crc32_benchmark.php
	$(PHP_CS_FIXER) fix --dry-run --diff tests
	$(PHP_CS_FIXER) fix --dry-run --diff ext/tests

benchmark: ext vendor
	$(PHP) -d extension=ext/modules/crc32c.so crc32_benchmark.php

test: ext vendor
	$(PHP) -v
	$(PHP) -d extension=ext/modules/crc32c.so $(PHP_UNIT) tests/

# Test all the local versions of PHP
test_all:
	for phpize in $$(ls $$(brew --prefix)/Cellar/php*/*/bin/phpize); do \
	  NO_INTERACTION=1 \
	  PHP_BIN=$$(dirname $$phpize) \
	  $(MAKE) clean test; \
	done

ext: ext/modules/crc32c.so

ext_test: ext
	NO_INTERACTION=1 $(MAKE) -C ext test

ext/modules/crc32c.so: ext/hash_crc32c.c ext/php_crc32c.c ext/php_crc32c.h
	cd ext && \
	./install_crc32c.sh && \
	$(PHPIZE) && \
	./configure \
	  --with-php-config=$(PHP_CONFIG) && \
	$(MAKE)
