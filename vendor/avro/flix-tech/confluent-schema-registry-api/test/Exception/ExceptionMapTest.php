<?php

declare(strict_types=1);

namespace FlixTech\SchemaRegistryApi\Test\Exception;

use FlixTech\SchemaRegistryApi\Exception\AbstractSchemaRegistryException;
use FlixTech\SchemaRegistryApi\Exception\BackendDataStoreException;
use FlixTech\SchemaRegistryApi\Exception\ExceptionMap;
use FlixTech\SchemaRegistryApi\Exception\IncompatibleAvroSchemaException;
use FlixTech\SchemaRegistryApi\Exception\InvalidAvroSchemaException;
use FlixTech\SchemaRegistryApi\Exception\InvalidCompatibilityLevelException;
use FlixTech\SchemaRegistryApi\Exception\InvalidVersionException;
use FlixTech\SchemaRegistryApi\Exception\MasterProxyException;
use FlixTech\SchemaRegistryApi\Exception\OperationTimedOutException;
use FlixTech\SchemaRegistryApi\Exception\SchemaNotFoundException;
use FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException;
use FlixTech\SchemaRegistryApi\Exception\SubjectNotFoundException;
use FlixTech\SchemaRegistryApi\Exception\VersionNotFoundException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ExceptionMapTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_handle_InvalidAvroSchema_code()
    {
        $this->assertSchemaRegistryException(
            InvalidAvroSchemaException::class,
            'Invalid Avro schema',
            42201,
            (ExceptionMap::instance())(
                new RequestException(
                '422 Unprocessable Entity',
                    new Request('GET', '/'),
                    new Response(
                        422,
                        ['Content-Type' => 'application/vnd.schemaregistry.v1+json'],
                        '{"error_code":42201,"message": "Invalid Avro schema"}'
                    )
                )
            )
        );
    }

    /**
     * @test
     */
    public function it_should_handle_IncompatibleAvroSchema_code()
    {
        $this->assertSchemaRegistryException(
            IncompatibleAvroSchemaException::class,
            'Incompatible Avro schema',
            409,
            (ExceptionMap::instance())(
                new RequestException(
                    '409 Conflict',
                    new Request('GET', '/'),
                    new Response(
                        409,
                        ['Content-Type' => 'application/vnd.schemaregistry.v1+json'],
                        '{"error_code":409,"message": "Incompatible Avro schema"}'
                    )
                )
            )
        );
    }

    /**
     * @test
     */
    public function it_should_handle_BackendDataStore_code()
    {
        $this->assertSchemaRegistryException(
            BackendDataStoreException::class,
            'Error in the backend datastore',
            50001,
            (ExceptionMap::instance())(
                new RequestException(
                    '500 Internal Server Error',
                    new Request('GET', '/'),
                    new Response(
                        500,
                        ['Content-Type' => 'application/vnd.schemaregistry.v1+json'],
                        '{"error_code":50001,"message": "Error in the backend datastore"}'
                    )
                )
            )
        );
    }

    /**
     * @test
     */
    public function it_should_handle_InvalidCompatibilityLevel_code()
    {
        $this->assertSchemaRegistryException(
            InvalidCompatibilityLevelException::class,
            'Invalid compatibility level',
            42203,
            (ExceptionMap::instance())(
                new RequestException(
                    '422 Unprocessable Entity',
                    new Request('GET', '/'),
                    new Response(
                        422,
                        ['Content-Type' => 'application/vnd.schemaregistry.v1+json'],
                        '{"error_code":42203,"message": "Invalid compatibility level"}'
                    )
                )
            )
        );
    }

    /**
     * @test
     */
    public function it_should_handle_InvalidVersion_code()
    {
        $this->assertSchemaRegistryException(
            InvalidVersionException::class,
            'Invalid version',
            42202,
            (ExceptionMap::instance())(
                new RequestException(
                    '422 Unprocessable Entity',
                    new Request('GET', '/'),
                    new Response(
                        422,
                        ['Content-Type' => 'application/vnd.schemaregistry.v1+json'],
                        '{"error_code":42202,"message": "Invalid version"}'
                    )
                )
            )
        );
    }

    /**
     * @test
     */
    public function it_should_handle_MasterProxy_code()
    {
        $this->assertSchemaRegistryException(
            MasterProxyException::class,
            'Error while forwarding the request to the master',
            50003,
            (ExceptionMap::instance())(
                new RequestException(
                    '500 Internal server Error',
                    new Request('GET', '/'),
                    new Response(
                        500,
                        ['Content-Type' => 'application/vnd.schemaregistry.v1+json'],
                        '{"error_code":50003,"message": "Error while forwarding the request to the master"}'
                    )
                )
            )
        );
    }

    /**
     * @test
     */
    public function it_should_handle_OperationTimedOut_code()
    {
        $this->assertSchemaRegistryException(
            OperationTimedOutException::class,
            'Operation timed out',
            50002,
            (ExceptionMap::instance())(
                new RequestException(
                    '500 Internal server Error',
                    new Request('GET', '/'),
                    new Response(
                        500,
                        ['Content-Type' => 'application/vnd.schemaregistry.v1+json'],
                        '{"error_code":50002,"message": "Operation timed out"}'
                    )
                )
            )
        );
    }

    /**
     * @test
     */
    public function it_should_handle_SchemaNotFound_code()
    {
        $this->assertSchemaRegistryException(
            SchemaNotFoundException::class,
            'Schema not found',
            40403,
            (ExceptionMap::instance())(
                new RequestException(
                    '404 Not Found',
                    new Request('GET', '/'),
                    new Response(
                        404,
                        ['Content-Type' => 'application/vnd.schemaregistry.v1+json'],
                        '{"error_code":40403,"message": "Schema not found"}'
                    )
                )
            )
        );
    }

    /**
     * @test
     */
    public function it_should_handle_SubjectNotFound_code()
    {
        $this->assertSchemaRegistryException(
            SubjectNotFoundException::class,
            'Subject not found',
            40401,
            (ExceptionMap::instance())(
                new RequestException(
                    '404 Not Found',
                    new Request('GET', '/'),
                    new Response(
                        404,
                        ['Content-Type' => 'application/vnd.schemaregistry.v1+json'],
                        '{"error_code":40401,"message": "Subject not found"}'
                    )
                )
            )
        );
    }

    /**
     * @test
     */
    public function it_should_handle_VersionNotFound_code()
    {
        $this->assertSchemaRegistryException(
            VersionNotFoundException::class,
            'Version not found',
            40402,
            (ExceptionMap::instance())(
                new RequestException(
                    '404 Not Found',
                    new Request('GET', '/'),
                    new Response(
                        404,
                        ['Content-Type' => 'application/vnd.schemaregistry.v1+json'],
                        '{"error_code":40402,"message": "Version not found"}'
                    )
                )
            )
        );
    }

    /**
     * @test
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage RequestException has no response to inspect
     */
    public function it_should_not_process_exceptions_with_missing_response()
    {
        (ExceptionMap::instance())(
            new RequestException(
                '404 Not Found',
                new Request('GET', '/')
            )
        );
    }

    /**
     * @test
     *
     * @expectedException \LogicException
     */
    public function it_will_check_for_invalid_schema_registry_exceptions_not_defining_a_code()
    {
        InvalidNewSchemaRegistryException::errorCode();
    }

    /**
     * @test
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid message body received - cannot find "error_code" field in response body
     */
    public function it_should_not_process_exceptions_with_missing_error_codes()
    {
        (ExceptionMap::instance())(
            new RequestException(
                '404 Not Found',
                new Request('GET', '/'),
                new Response(
                    404,
                    ['Content-Type' => 'application/vnd.schemaregistry.v1+json'],
                    '{"message": "This JSON has no \'error_code\' field."}'
                )
            )
        );
    }

    /**
     * @test
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unknown error code "99999"
     */
    public function it_should_not_process_unknown_error_codes()
    {
        (ExceptionMap::instance())(
            new RequestException(
                '404 Not Found',
                new Request('GET', '/'),
                new Response(
                    404,
                    ['Content-Type' => 'application/vnd.schemaregistry.v1+json'],
                    '{"error_code":99999,"message": "Subject not found"}'
                )
            )
        );
    }

    private function assertSchemaRegistryException(
        string $exceptionClass,
        string $expectedMessage,
        int $errorCode,
        SchemaRegistryException $exception
    ) {
        $this->assertInstanceOf($exceptionClass, $exception);
        $this->assertEquals($errorCode, $exception->getCode());
        $this->assertEquals($expectedMessage, $exception->getMessage());
    }
}

class InvalidNewSchemaRegistryException extends AbstractSchemaRegistryException
{
}
