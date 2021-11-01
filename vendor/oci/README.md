# Oracle Cloud Infrastructure SDK for PHP

## About

oci-php-sdk provides an SDK for PHP that you can use to manage your Oracle Cloud Infrastructure resources.

The project is open source and maintained by Oracle Corp. The home page for the project is [here](https://docs.oracle.com/en-us/iaas/Content/API/Concepts/sdks.htm).

## Requirements

* PHP 5.6

## Installation

TODO

## Examples

Examples can be found [here](/src/Oracle/Oci/Examples/).

You may run any example by invoking the `php` command with the example you want to run,
for example: `php src/Oracle/Oci/Examples/ObjectStorageExample.php`

## Documentation

TODO

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
