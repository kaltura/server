# Oracle Cloud Infrastructure SDK for PHP TODO

## For Kaltura

### Requests
- encode path parameters -- some special UTF-8 characters are not handled correctly by Guzzle
- check if we should set JSON_UNESCAPED_SLASHES for all json_encode calls

### Major Features
- Upload Manager (uploads automatically broken into smaller parts)
- Paginator (multi-page lists retrieved transparently to the customer)
- Instance Principals authentication
- PHP stream wrapper

### Test
- Multipart upload operations
- Work request operations
- PreAuthenticatedRequest (PAR) operations

## Later

### Refactoring
- refactor calling GuzzleClient (introduce `callApi` / `callApiAsync` methods in `AbstractClient`) as [recommended by Ziyao](https://bitbucket.oci.oraclecorp.com/projects/SDK/repos/oci-php-sdk/pull-requests/12/overview)

### Data Types
- enums
- top-level enums
- enum-refs
- inline enums

### Regions and Realms
- IMDS
- config file
- environment variable

### Data Types
- models (?)

### Convenience
- waiters
- retries
- circuit breakers
- config file

### Documentation
- installation
- examples
- help
