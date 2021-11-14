# Oracle Cloud Infrastructure SDK for PHP

## About

oci-php-sdk provides an SDK for PHP that you can use to manage your Oracle Cloud Infrastructure resources.

The project is open source and maintained by Oracle Corp. The home page for the project is [here](https://docs.oracle.com/en-us/iaas/Content/API/Concepts/sdks.htm).

## Requirements

* PHP 5.6

## Installation

TODO

### Consuming the oci-php-sdk from a zip file

1. Create a directory, e.g. `/path/to/artifacts`
2. Place the internal OCI PHP SDK zip file, e.g. `oci-php-sdk-0.0.1-2021-11-02T13-44-29.zip`, in that directory.
    - If you have multiple versions of the zip file, make sure the directory only contains the version you want to use.
3. Add a dependency on `oracle/oci-php-sdk` version `0.0.1` to your `composer.json` file
4. Add a repository of type `artifact` to your `composer.json` file, where `url` is set to the directory with the zip file, e.g. `/path/to/artifacts`

Here is a complete example of such a `composer.json` file:

```
{
    "name": "oracle/oci-php-sdk-consumer",
    "type": "project",
    "require": {
        "oracle/oci-php-sdk": "0.0.1"
    },
    "repositories": [
        {
            "type": "artifact",
            "url": "/path/to/artifacts"
        }
    ]
}
```

5. Clear your Composer cache, `vendor` directory, and `composer.lock` file:
```
composer clear-cache && rm -rf vendor; rm composer.lock
```
   - This helps make sure that `vendor` contains the latest contents from the zip file.
6. Run `composer install`
7. In your source file, e.g. `src/test.php`, require the `vendor/autoload.php` file, and then use the OCI PHP SDK classes you need. For example:

```
<?php

require 'vendor/autoload.php';

use Oracle\Oci\Common\UserAgent;

echo "UserAgent: " . UserAgent::getUserAgent() . PHP_EOL;

?>
```
8. Run the test: `php src/test.php`

### Consuming the oci-php-sdk from a Git repository

1. Add a dependency on `oracle/oci-php-sdk` version `dev-master` to your `composer.json` file
2. Add a repository of type `git` to your `composer.json` file, where `url` is set to the same URL you use to `git clone`, e.g. `https://github.com/organization/my-repo.git`
   - **Note**: `https://github.com/organization/my-repo.git` is not an actual Git repository with the OCI PHP SDK.

Here is a complete example of such a `composer.json` file:

```
{
    "name": "oracle/oci-php-sdk-consumer",
    "type": "project",
    "require": {
        "oracle/oci-php-sdk": "dev-master"
    },
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/organization/my-repo.git"
        }
    ]
}
```

3. Clear your Composer cache, `vendor` directory, and `composer.lock` file:
```
composer clear-cache && rm -rf vendor; rm composer.lock
```
   - This helps make sure that `vendor` contains the latest contents from the Git `master` branch
4. Run `composer install`
5. In your source file, e.g. `src/test.php`, require the `vendor/autoload.php` file, and then use the OCI PHP SDK classes you need. For example:

```
<?php

require 'vendor/autoload.php';

use Oracle\Oci\Common\UserAgent;

echo "UserAgent: " . UserAgent::getUserAgent() . PHP_EOL;

?>
```

8. Run the test: `php src/test.php`

## Examples

Examples can be found [here](/tests/Oracle/Oci/Examples/).

You may run any example by invoking the `php` command with the example you want to run,
for example: `php tests/Oracle/Oci/Examples/ObjectStorageExample.php`

## Documentation

TODO

### Installing PHP 5.6 on Oracle Linux

The following has worked for installing PHP 5.6 on Oracle Linux:

    sudo yum -y remove oracle-epel-release-el7
    sudo yum -y install https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
    sudo yum -y install https://rpms.remirepo.net/enterprise/remi-release-7.rpm
    sudo yum -y install yum-utils
    sudo yum-config-manager --enable remi-php73
    sudo yum -y install php56
    sudo yum -y install php56-php-mbstring.x86_64
    alias php=php56
    php -v
    mkdir ~/.oci

### Installing Composer

Install Composer as package manager for PHP:

    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
    php composer-setup.php
    php -r "unlink('composer-setup.php');"
    sudo mv composer.phar /usr/local/bin/composer

### Running the Instance Principals Example

The [`tests/Oracle/Oci/Examples/InstancePrincipalsExample.php`](/tests/Oracle/Oci/Examples/InstancePrincipalsExample.php) and [`tests/Oracle/Oci/Examples/CachingInstancePrincipalsExample.php`](/tests/Oracle/Oci/Examples/CachingInstancePrincipalsExample.php) examples must be run on an OCI instance. To set it up, you can follow these steps:

1. Create a dynamic group. You can use a matching rule like this to get all instances in a certain compartment:
    ```
    Any {instance.compartment.id = '<ocid-of-compartment>'}
    ```

2. Start an OCI instance. Make sure that it is matched by the dynamic group, e.g. by creating it in the correct compartment.
3. Create a policy for the dynamic group that grants the desired permissions. For example:
    ```
    Allow dynamic-group <name-of-dynamic-group> to manage buckets in compartment <name-of-compartment>
    Allow dynamic-group <name-of-dynamic-group> to manage objects in compartment <name-of-compartment>
    Allow dynamic-group <name-of-dynamic-group> to manage objectstorage-namespaces in compartment <name-of-compartment>
    ```
4. Install PHP 5.6 on Oracle Linux. See above.
5. Copy the OCI PHP SDK and this example to the OCI instance (using `scp` or `rsync`).
6. Run Composer to download the required packages:
    ```
    composer update
    composer install
    ```
7. SSH into the OCI instance.
8. Run the example:
    ```
    php tests/Oracle/Oci/Examples/InstancePrincipalsExample.php
    ```
9. Run the Instance Principal-specific unit tests:
    ```
    php vendor/bin/phpunit --group InstancePrincipalsRequired
    ```

## Help

TODO

## Changes

See [CHANGELOG](/CHANGELOG.md).

## Contributing

oci-php-sdk is an open source project. See [CONTRIBUTING](/CONTRIBUTING.md) for details.

Oracle gratefully acknowledges the contributions to oci-php-sdk that have been made by the community.

## Known Issues

You can find information on any known issues with the SDK [here](https://docs.cloud.oracle.com/iaas/Content/knownissues.htm).

### Thread Safety

The OCI PHP SDK is based on the `GuzzleHttp\Client`; therefore, it has the same threading behavior as `GuzzleHttp\Client`.
 
There does not appear to be good documentation on the thread safety of `GuzzleHttp\Client`, but there are hints that it is _NOT_ thread-safe:

1. "Are you using threads (like with pthreads)? Guzzle is not thread safe and will not work in a multithreaded application" [1](https://github.com/guzzle/guzzle/issues/1504)
2. "I would guess that the underlying cURL handles, and PHP's integration with cURL, have an issue being shared across multiple threads. You may have to create unique clients for each thread." [2](https://github.com/guzzle/guzzle/issues/1398)
 
These issues are not unique to the OCI PHP SDK, and the solution seems to simply be to create a separate client per thread.

## License

Copyright (c) 2021, Oracle and/or its affiliates.  All rights reserved.
This software is dual-licensed to you under the Universal Permissive License (UPL) 1.0 as shown at https://oss.oracle.com/licenses/upl
or Apache License 2.0 as shown at http://www.apache.org/licenses/LICENSE-2.0. You may choose either license.

See [LICENSE](/LICENSE.txt) for more details.
