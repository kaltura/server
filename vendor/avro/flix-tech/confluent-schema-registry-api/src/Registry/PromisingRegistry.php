<?php

declare(strict_types=1);

namespace FlixTech\SchemaRegistryApi\Registry;

use AvroSchema;
use FlixTech\SchemaRegistryApi\AsynchronousRegistry;
use FlixTech\SchemaRegistryApi\Exception\ExceptionMap;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use const FlixTech\SchemaRegistryApi\Constants\VERSION_LATEST;
use function FlixTech\SchemaRegistryApi\Requests\checkIfSubjectHasSchemaRegisteredRequest;
use function FlixTech\SchemaRegistryApi\Requests\registerNewSchemaVersionWithSubjectRequest;
use function FlixTech\SchemaRegistryApi\Requests\schemaRequest;
use function FlixTech\SchemaRegistryApi\Requests\singleSubjectVersionRequest;
use function FlixTech\SchemaRegistryApi\Requests\validateSchemaId;
use function FlixTech\SchemaRegistryApi\Requests\validateVersionId;

/**
 * {@inheritdoc}
 */
class PromisingRegistry implements AsynchronousRegistry
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var \Closure
     */
    private $rejectedCallback;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
        $this->rejectedCallback = function (\Exception $exception) {
            return (ExceptionMap::instance())($exception);
        };
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function register(string $subject, AvroSchema $schema, callable $requestCallback = null): PromiseInterface
    {
        $request = registerNewSchemaVersionWithSubjectRequest((string) $schema, $subject);

        $onFulfilled = function (ResponseInterface $response) {
            $schemaId = $this->getJsonFromResponseBody($response)['id'];

            return $schemaId;
        };

        return $this->makeRequest($request, $onFulfilled, $requestCallback);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function schemaId(string $subject, AvroSchema $schema, callable $requestCallback = null): PromiseInterface
    {
        $request = checkIfSubjectHasSchemaRegisteredRequest($subject, (string) $schema);

        $onFulfilled = function (ResponseInterface $response) {
            $decodedResponse = $this->getJsonFromResponseBody($response);

            return $decodedResponse['id'];
        };

        return $this->makeRequest($request, $onFulfilled, $requestCallback);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function schemaForId(int $schemaId, callable $requestCallback = null): PromiseInterface
    {
        $request = schemaRequest(validateSchemaId($schemaId));

        $onFulfilled = function (ResponseInterface $response) {
            $schema = AvroSchema::parse(
                $this->getJsonFromResponseBody($response)['schema']
            );

            return $schema;
        };

        return $this->makeRequest($request, $onFulfilled, $requestCallback);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function schemaForSubjectAndVersion(string $subject, int $version, callable $requestCallback = null): PromiseInterface
    {
        $request = singleSubjectVersionRequest($subject, validateVersionId($version));

        $onFulfilled = function (ResponseInterface $response) {
            $schema = AvroSchema::parse(
                $this->getJsonFromResponseBody($response)['schema']
            );

            return $schema;
        };

        return $this->makeRequest($request, $onFulfilled, $requestCallback);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function schemaVersion(string $subject, AvroSchema $schema, callable $requestCallback = null): PromiseInterface
    {
        $request = checkIfSubjectHasSchemaRegisteredRequest($subject, (string) $schema);

        $onFulfilled = function (ResponseInterface $response) {
            return $this->getJsonFromResponseBody($response)['version'];
        };

        return $this->makeRequest($request, $onFulfilled, $requestCallback);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function latestVersion(string $subject, callable $requestCallback = null): PromiseInterface
    {
        $request = singleSubjectVersionRequest($subject, VERSION_LATEST);

        $onFulfilled = function (ResponseInterface $response) {
            $schema = AvroSchema::parse(
                $this->getJsonFromResponseBody($response)['schema']
            );

            return $schema;
        };

        return $this->makeRequest($request, $onFulfilled, $requestCallback);
    }

    /**
     * @param RequestInterface $request
     * @param callable         $onFulfilled
     * @param callable|null    $requestCallback
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    private function makeRequest(RequestInterface $request, callable $onFulfilled, callable $requestCallback = null): PromiseInterface
    {
        return $this->client
            ->sendAsync(null !== $requestCallback ? $requestCallback($request) : $request)
            ->then($onFulfilled, $this->rejectedCallback);
    }

    private function getJsonFromResponseBody(ResponseInterface $response): array
    {
        $body = (string) $response->getBody();

        try {
            return \GuzzleHttp\json_decode($body, true);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(
                \sprintf('%s - with content "%s"', $e->getMessage(), $body),
                $e->getCode(),
                $e
            );
        }
    }
}
