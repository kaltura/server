<?php

declare(strict_types=1);

namespace FlixTech\SchemaRegistryApi\Test\Registry;

use AvroSchema;
use FlixTech\SchemaRegistryApi\Exception\SchemaNotFoundException;
use FlixTech\SchemaRegistryApi\Registry\PromisingRegistry;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use const FlixTech\SchemaRegistryApi\Constants\VERSION_LATEST;
use function FlixTech\SchemaRegistryApi\Requests\checkIfSubjectHasSchemaRegisteredRequest;
use function FlixTech\SchemaRegistryApi\Requests\registerNewSchemaVersionWithSubjectRequest;
use function FlixTech\SchemaRegistryApi\Requests\schemaRequest;
use function FlixTech\SchemaRegistryApi\Requests\singleSubjectVersionRequest;
use function FlixTech\SchemaRegistryApi\Requests\validateSchemaId;
use function FlixTech\SchemaRegistryApi\Requests\validateVersionId;

class PromisingRegistryTest extends TestCase
{
    /**
     * @var Client
     */
    private $clientMock;

    /**
     * @var PromisingRegistry
     */
    private $registry;

    /**
     * @var MockHandler
     */
    private $mockHandler;

    /**
     * @test
     */
    public function it_should_register_schemas()
    {
        $responses = [
            new Response(200, [], '{"id": 3}')
        ];
        $subject = 'test';
        $schema = AvroSchema::parse('{"type": "string"}');
        $expectedRequest = registerNewSchemaVersionWithSubjectRequest((string) $schema, $subject);

        $this->registry = new PromisingRegistry($this->clientWithMockResponses($responses));

        $promise = $this->registry->register(
            $subject,
            $schema,
            $this->assertRequestCallable($expectedRequest)
        );

        $this->assertEquals(3, $promise->wait());
    }

    /**
     * @test
     */
    public function it_can_get_the_schema_id_for_a_schema_and_subject()
    {
        $responses = [
            new Response(200, [], '{"id": 2}')
        ];
        $subject = 'test';
        $schema = AvroSchema::parse('{"type": "string"}');
        $expectedRequest = checkIfSubjectHasSchemaRegisteredRequest($subject, (string) $schema);

        $this->registry = new PromisingRegistry($this->clientWithMockResponses($responses));

        $promise = $this->registry->schemaId(
            $subject,
            $schema,
            $this->assertRequestCallable($expectedRequest)
        );

        $this->assertEquals(2, $promise->wait());
    }

    /**
     * @test
     */
    public function it_can_get_a_schema_for_id()
    {
        $responses = [
            new Response(200, [], '{"schema": "\"string\""}')
        ];
        $schema = AvroSchema::parse('"string"');
        $expectedRequest = schemaRequest(validateSchemaId(1));

        $this->registry = new PromisingRegistry($this->clientWithMockResponses($responses));

        $promise = $this->registry->schemaForId(
            1,
            $this->assertRequestCallable($expectedRequest)
        );

        $this->assertEquals($schema, $promise->wait());
    }

    /**
     * @test
     */
    public function it_can_get_a_schema_for_subject_and_version()
    {
        $responses = [
            new Response(200, [], '{"schema": "\"string\""}')
        ];
        $subject = 'test';
        $version = 2;
        $schema = AvroSchema::parse('{"type": "string"}');
        $expectedRequest = singleSubjectVersionRequest($subject, validateVersionId($version));

        $this->registry = new PromisingRegistry($this->clientWithMockResponses($responses));

        $promise = $this->registry->schemaForSubjectAndVersion(
            $subject,
            $version,
            $this->assertRequestCallable($expectedRequest)
        );

        $this->assertEquals($schema, $promise->wait());
    }

    /**
     * @test
     */
    public function it_can_get_the_schema_version()
    {
        $responses = [
            new Response(200, [], '{"version": 3}')
        ];
        $subject = 'test';
        $schema = AvroSchema::parse('{"type": "string"}');
        $expectedRequest = checkIfSubjectHasSchemaRegisteredRequest($subject, (string) $schema);

        $this->registry = new PromisingRegistry($this->clientWithMockResponses($responses));

        $promise = $this->registry->schemaVersion(
            $subject,
            $schema,
            $this->assertRequestCallable($expectedRequest)
        );

        $this->assertEquals(3, $promise->wait());
    }

    /**
     * @test
     */
    public function it_can_get_the_latest_version()
    {
        $responses = [
            new Response(200, [], '{"schema": "\"string\""}')
        ];

        $subject = 'test';
        $schema = AvroSchema::parse('{"type": "string"}');
        $expectedRequest = singleSubjectVersionRequest($subject, VERSION_LATEST);

        $this->registry = new PromisingRegistry($this->clientWithMockResponses($responses));

        $promise = $this->registry->latestVersion(
            $subject,
            $this->assertRequestCallable($expectedRequest)
        );

        $this->assertEquals($schema, $promise->wait());
    }

    /**
     * @test
     */
    public function it_will_not_throw_but_pass_exceptions()
    {
        $responses = [
            new Response(
                404,
                [],
                sprintf('{"error_code": %d, "message": "test"}', SchemaNotFoundException::ERROR_CODE)
            )
        ];
        $this->registry = new PromisingRegistry($this->clientWithMockResponses($responses));

        /** @var \Exception $exception */
        $exception = $this->registry->schemaForId(1)->wait();

        $this->assertInstanceOf(SchemaNotFoundException::class, $exception);
        $this->assertEquals('test', $exception->getMessage());
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface[] $responses
     *
     * @return Client
     */
    private function clientWithMockResponses(array $responses): Client
    {
        $this->mockHandler = new MockHandler($responses);
        $stack = HandlerStack::create($this->mockHandler);

        $this->clientMock = new Client(['handler' => $stack]);

        return $this->clientMock;
    }

    private function assertRequestCallable(RequestInterface $expectedRequest): callable
    {
        return function (RequestInterface $actual) use ($expectedRequest) {
            $this->assertEquals($expectedRequest->getUri(), $actual->getUri());
            $this->assertEquals($expectedRequest->getHeaders(), $actual->getHeaders());
            $this->assertEquals($expectedRequest->getMethod(), $actual->getMethod());
            $this->assertEquals($expectedRequest->getBody()->getContents(), $actual->getBody()->getContents());

            return $actual;
        };
    }
}
