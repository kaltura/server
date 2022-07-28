# Confluent Schema Registry PHP API

[![Build Status](https://travis-ci.org/flix-tech/schema-registry-php-client.svg?branch=2.0.2)](https://travis-ci.org/flix-tech/schema-registry-php-client)
[![Latest Stable Version](https://poser.pugx.org/flix-tech/confluent-schema-registry-api/v/stable)](https://packagist.org/packages/flix-tech/confluent-schema-registry-api)
[![Total Downloads](https://poser.pugx.org/flix-tech/confluent-schema-registry-api/downloads)](https://packagist.org/packages/flix-tech/confluent-schema-registry-api)
[![License](https://poser.pugx.org/flix-tech/confluent-schema-registry-api/license)](https://packagist.org/packages/flix-tech/confluent-schema-registry-api)

A PHP 7.0+ library to consume the Confluent Schema Registry REST API. It provides low level functions to create PSR-7
compliant requests that can be used as well as high level abstractions to ease developer experience.

#### Contents

- [Requirements](#requirements)
  - [Hard Dependencies](#hard-dependencies)
  - [Optional Dependencies](#optional-dependencies)
- [Installation](#installation)
- [Compatibility](#compatibility)
- [Usage](#usage)
  - [Asynchronous API](#asynchronous-api)
  - [Synchronous API](#synchronous-api)
  - [Caching](#caching)
  - [Low Level API](#low-level-api)
- [Testing](#testing)
  - [Unit tests, Coding standards and static analysis](#unit-tests-coding-standards-and-static-analysis)
  - [Integration tests](#integration-tests)
- [Contributing](#contributing)

## Requirements

### Hard dependencies

| Dependency | Version | Reason |
|:--- |:---:|:--- |
| **`php`** | ~7.0 | Anything lower has reached EOL |
| **`guzzlephp/guzzle`** | ~6.3 | Using `Request` to build PSR-7 `RequestInterface` |
| **`beberlei/assert`** | ~2.7 | The de-facto standard assertions library for PHP |
| **`flix-tech/avro-php`** | ^2.0 | Maintained fork of the only Avro PHP implementation: `rg/avro-php` |

### Optional dependencies

| Dependency | Version | Reason |
|:--- |:---:|:--- |
| **`doctrine/cache`** | ~1.3 | If you want to use the `DoctrineCacheAdapter` |
| **`raphhh/trex-reflection`** | ~1.0 | If you want to use the `RequestCallbackValidator`s |

## Installation

This library is installed via [`composer`](http://getcomposer.org).

```bash
composer require "flix-tech/confluent-schema-registry-api=~4.0"
```

## Compatibility

This library follows strict semantic versioning, so you can expect any minor and patch release to be compatible, while
major version upgrades will have incompatibilities that will be released in the UPGRADE.md file.


## Usage

### Asynchronous API

[Interface declaration](src/AsynchronousRegistry.php)

#### Example `PromisingRegistry`

```php
<?php

use GuzzleHttp\Client;
use FlixTech\SchemaRegistryApi\Registry\PromisingRegistry;
use FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException;
use Psr\Http\Message\RequestInterface;

$registry = new PromisingRegistry(
    new Client(['base_uri' => 'registry.example.com'])
);

// Register a schema with a subject
$schema = AvroSchema::parse('{"type": "string"}');

// The promise will either contain a schema id as int when fulfilled,
// or a SchemaRegistryException instance when rejected.
// If the subject does not exist, it will be created implicitly
$promise = $registry->register('test-subject', $schema);

// If you want to resolve the promise, you might either get the value or an instance of a SchemaRegistryException
// It is more like an Either Monad, since returning Exceptions from rejection callbacks will throw them.
// We want to leave that decision to the user of the lib.
// TODO: Maybe return an Either Monad instead
$promise = $promise->then(
    function ($schemaIdOrSchemaRegistryException) {
        if ($schemaIdOrSchemaRegistryException instanceof SchemaRegistryException) {
            throw $schemaIdOrSchemaRegistryException;
        }
        
        return $schemaIdOrSchemaRegistryException;
    }
);

// Resolve the promise
$schemaId = $promise->wait();

// Get a schema by schema id
$promise = $registry->schemaForId($schemaId);
// As above you could add additional callbacks to the promise
$schema = $promise->wait();

// Get the version of a schema for a given subject.
// All methods also have a request callback third parameter.
// It takes a `Psr\Http\Message\RequestInterface` and should return a `Psr\Http\Message\RequestInterface`
$version = $registry->schemaVersion(
    'test-subject',
    $schema,
    function (RequestInterface $request) {
        return $request->withAddedHeader('Cache-Control', 'no-cache');
    }
)->wait();

// You can also get a schema by subject and version
$schema = $registry->schemaForSubjectAndVersion('test-subject', $version)->wait();

// You can additionally just query for the currently latest schema within a subject.
// *NOTE*: Once you requested this it might not be the latest version anymore.
$latestSchema = $registry->latestVersion('test-subject')->wait();

// Sometimes you want to find out the global schema id for a given schema
$schemaId = $registry->schemaId('test-subject', $schema)->wait();
```

### Synchronous API

[Interface declaration](src/SynchronousRegistry.php)

#### Example `BlockingRegistry`

```php
<?php

use FlixTech\SchemaRegistryApi\Registry\BlockingRegistry;
use FlixTech\SchemaRegistryApi\Registry\PromisingRegistry;
use GuzzleHttp\Client;

$registry = new BlockingRegistry(
    new PromisingRegistry(
        new Client(['base_uri' => 'registry.example.com'])
    )
);

// What the blocking registry does is actually resolving the promises
// with `wait` and adding a throwing rejection callback.
$schema = AvroSchema::parse('{"type": "string"}');

// This will be an int, and not a promise
$schemaId = $registry->register('test-subject', $schema);
```

### Caching

There is a `CachedRegistry` that accepts a `CacheAdapter` together with a `Registry`.
It supports both async and sync APIs.

> **NOTE:**
>
> From version 4.x of this library the API for the `CacheAdapterInterface` has been changed in order to allow caching
> of schema ids by hash of a given schema.

#### Example

```php
<?php

use FlixTech\SchemaRegistryApi\Registry\BlockingRegistry;
use FlixTech\SchemaRegistryApi\Registry\PromisingRegistry;
use FlixTech\SchemaRegistryApi\Registry\CachedRegistry;
use FlixTech\SchemaRegistryApi\Registry\Cache\AvroObjectCacheAdapter;
use FlixTech\SchemaRegistryApi\Registry\Cache\DoctrineCacheAdapter;
use Doctrine\Common\Cache\ArrayCache;
use GuzzleHttp\Client;

$asyncApi = new PromisingRegistry(
    new Client(['base_uri' => 'registry.example.com'])
);

$syncApi = new BlockingRegistry($asyncApi);

$doctrineCachedSyncApi = new CachedRegistry(
    $asyncApi,
    new DoctrineCacheAdapter(
        new ArrayCache()
    )
);

// All adapters support both APIs, for async APIs additional fulfillment callbacks will be registered.
$avroObjectCachedAsyncApi = new CachedRegistry(
    $syncApi,
    new AvroObjectCacheAdapter()
);

// NEW in version 4.x, passing in custom hash functions to cache schema ids via the schema hash
// By default the following function is used internally
$defaultHashFunction = function (AvroSchema $schema) {
   return md5((string) $schema); 
};

// You can also define your own hash callable
$sha1HashFunction = function (AvroSchema $schema) {
   return sha1((string) $schema); 
};

// Pass the hash function as optional 3rd parameter to the CachedRegistry constructor
$avroObjectCachedAsyncApi = new CachedRegistry(
    $syncApi,
    new AvroObjectCacheAdapter(),
    $sha1HashFunction
);
```

### Low Level API

There is a low-level API that provides simple functions that return PSR-7 request objects for the different endpoints of
the registry. See [Requests/Functions](src/Requests/Functions.php) for more information.

There are also requests to use the new `DELETE` API of the schema registry.

## Testing

This library uses a `Makefile` to run the test suite.

#### Unit tests, Coding standards and static analysis

```bash
make quick-test
```

#### Integration tests

This library uses a `docker-compose` configuration to fire up a schema registry for integration testing, hence
`docker-compose` from version 1.13.0 is required to run those tests.

```bash
make integration-test
make clean
```

## Contributing

In order to contribute to this library, follow this workflow:

- Fork the repository
- Create a feature branch
- Work on the feature 
- Run tests to verify that the tests are passing
- Open a PR to the upstream
- Be happy about contributing to open source!
