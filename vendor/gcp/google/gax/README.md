Google API Core for PHP
=================================

[![Build Status](https://api.travis-ci.org/googleapis/gax-php.svg?branch=master)](https://travis-ci.org/googleapis/gax-php)

[![Code Coverage](https://img.shields.io/codecov/c/github/googleapis/gax-php.svg)](https://codecov.io/github/googleapis/gax-php)

- [Documentation](http://googleapis.github.io/gax-php)

Google API Core for PHP (gax-php) is a set of modules which aids
the development of APIs for clients based on [gRPC][] and Google API
conventions.

Application code will rarely need to use most of the classes within this library
directly, but code generated automatically from the API definition files in
[Google APIs][] can use services such as page streaming and retry to
provide a more convenient and idiomatic API surface to callers.

[gRPC]: http://grpc.io
[Google APIs]: https://github.com/googleapis/googleapis/


PHP Versions
----------------

gax-php currently requires PHP 5.5 or higher.


Contributing
------------

Contributions to this library are always welcome and highly encouraged.

See the [CONTRIBUTING][] documentation for more information on how to get started.

[CONTRIBUTING]: https://github.com/googleapis/gax-php/blob/master/.github/CONTRIBUTING.md


Versioning
----------

This library follows [Semantic Versioning][].

This library is considered GA (generally available). As such, it will not introduce backwards-incompatible changes in any minor or patch releases. We will address issues and requests with the highest priority.

[Semantic Versioning]: http://semver.org/


Repository Structure
-------

All code lives under the src/ directory. Handwritten code lives in the
src/ApiCore directory and is contained in the `Google\ApiCore` namespace.

Generated classes for protobuf common types and LongRunning client live under
the src/ directory, in the appropriate directory and namespace.

Code in the metadata/ directory is provided to support generated protobuf
classes, and should not be used directly.


License
-------

BSD - See [LICENSE][] for more information.

[LICENSE]: https://github.com/googleapis/gax-php/blob/master/LICENSE
