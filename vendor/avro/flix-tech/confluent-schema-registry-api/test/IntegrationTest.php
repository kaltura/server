<?php

declare(strict_types=1);

namespace FlixTech\SchemaRegistryApi\Test;

use FlixTech\SchemaRegistryApi\Exception\ExceptionMap;
use FlixTech\SchemaRegistryApi\Exception\IncompatibleAvroSchemaException;
use FlixTech\SchemaRegistryApi\Exception\InvalidAvroSchemaException;
use FlixTech\SchemaRegistryApi\Exception\InvalidVersionException;
use FlixTech\SchemaRegistryApi\Exception\SchemaNotFoundException;
use FlixTech\SchemaRegistryApi\Exception\SubjectNotFoundException;
use FlixTech\SchemaRegistryApi\Exception\VersionNotFoundException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\UriTemplate;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use const FlixTech\SchemaRegistryApi\Constants\COMPATIBILITY_BACKWARD;
use const FlixTech\SchemaRegistryApi\Constants\COMPATIBILITY_FORWARD;
use const FlixTech\SchemaRegistryApi\Constants\COMPATIBILITY_FULL;
use const FlixTech\SchemaRegistryApi\Constants\VERSION_LATEST;
use function FlixTech\SchemaRegistryApi\Requests\allSubjectsRequest;
use function FlixTech\SchemaRegistryApi\Requests\allSubjectVersionsRequest;
use function FlixTech\SchemaRegistryApi\Requests\changeDefaultCompatibilityLevelRequest;
use function FlixTech\SchemaRegistryApi\Requests\changeSubjectCompatibilityLevelRequest;
use function FlixTech\SchemaRegistryApi\Requests\checkIfSubjectHasSchemaRegisteredRequest;
use function FlixTech\SchemaRegistryApi\Requests\checkSchemaCompatibilityAgainstVersionRequest;
use function FlixTech\SchemaRegistryApi\Requests\defaultCompatibilityLevelRequest;
use function FlixTech\SchemaRegistryApi\Requests\registerNewSchemaVersionWithSubjectRequest;
use function FlixTech\SchemaRegistryApi\Requests\schemaRequest;
use function FlixTech\SchemaRegistryApi\Requests\singleSubjectVersionRequest;
use function FlixTech\SchemaRegistryApi\Requests\subjectCompatibilityLevelRequest;

/**
 * Class IntegrationTest
 *
 * @group integration
 */
class IntegrationTest extends TestCase
{
    const SUBJECT_NAME = 'integration-test';
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $baseSchema = <<<SCHEMA
{
  "namespace": "example.avro",
  "type": "record",
  "name": "user",
  "fields": [
    {"name": "name", "type": "string"},
    {"name": "favorite_number",  "type": "int"}
  ]
}
SCHEMA;

    /**
     * @var string
     */
    private $compatibleSchemaEvolution = <<<COMPAT
{
  "namespace": "example.avro",
  "type": "record",
  "name": "user",
  "fields": [
    {"name": "name", "type": "string"},
    {"name": "favorite_number",  "type": "int"},
    {"name": "favorite_color", "type": "string", "default": "green"}
  ]
}
COMPAT;

    /**
     * @var string
     */
    private $incompatibleSchemaEvolution = <<<INCOMPATIBLE
{
  "namespace": "example.avro",
  "type": "record",
  "name": "user",
  "fields": [
    {"name": "name", "type": "string"},
    {"name": "favorite_number",  "type": "int"},
    {"name": "favorite_color", "type": "string"}
  ]
}
INCOMPATIBLE;

    /**
     * @var string
     */
    private $invalidSchema = '{"invalid": "invalid"}';


    protected function setUp()
    {
        if ((bool) getenv('ENABLE_INTEGRATION_TEST') === false) {
            self::markTestSkipped('Integration tests are disabled');
        }

        $this->client = new Client([
            'base_uri' => (new UriTemplate())->expand(
                'http://{host}:{port}',
                [
                    'host' => getenv('TEST_SCHEMA_REGISTRY_HOST'),
                    'port' => getenv('TEST_SCHEMA_REGISTRY_PORT'),
                ]
            )
        ]);
    }

    /**
     * @test
     */
    public function managing_subjects_and_versions()
    {
        $this->client
            ->sendAsync(allSubjectsRequest())
            ->then(
                function (ResponseInterface $request) {
                    $this->assertEmpty(\GuzzleHttp\json_decode($request->getBody()->getContents(), true));
                }
            )->wait();

        $this->client
            ->sendAsync(registerNewSchemaVersionWithSubjectRequest($this->baseSchema, self::SUBJECT_NAME))
            ->then(
                function (ResponseInterface $request) {
                    $this->assertEquals(1, \GuzzleHttp\json_decode($request->getBody()->getContents(), true)['id']);
                }
            )->wait();

        $this->client
            ->sendAsync(schemaRequest('1'))
            ->then(
                function (ResponseInterface $request) {
                    $decodedBody = \GuzzleHttp\json_decode($request->getBody()->getContents(), true);

                    $this->assertJsonStringEqualsJsonString($this->baseSchema, $decodedBody['schema']);
                }
            )->wait();

        $this->client
            ->sendAsync(checkIfSubjectHasSchemaRegisteredRequest(self::SUBJECT_NAME, $this->baseSchema))
            ->then(
                function (ResponseInterface $request) {
                    $decodedBody = \GuzzleHttp\json_decode($request->getBody()->getContents(), true);

                    $this->assertEquals(1, $decodedBody['id']);
                    $this->assertEquals(1, $decodedBody['version']);
                    $this->assertEquals(self::SUBJECT_NAME, $decodedBody['subject']);
                    $this->assertJsonStringEqualsJsonString($this->baseSchema, $decodedBody['schema']);
                }
            )->wait();

        $this->client
            ->sendAsync(singleSubjectVersionRequest(self::SUBJECT_NAME, VERSION_LATEST))
            ->then(
                function (ResponseInterface $request) {
                    $decodedBody = \GuzzleHttp\json_decode($request->getBody()->getContents(), true);

                    $this->assertEquals(self::SUBJECT_NAME, $decodedBody['subject']);
                    $this->assertEquals(1, $decodedBody['version']);
                    $this->assertJsonStringEqualsJsonString($this->baseSchema, $decodedBody['schema']);
                    $this->assertEquals(1, $decodedBody['id']);
                }
            )->wait();

        $this->client
            ->sendAsync(checkSchemaCompatibilityAgainstVersionRequest(
                $this->compatibleSchemaEvolution,
                self::SUBJECT_NAME,
                VERSION_LATEST
            ))->then(
                function (ResponseInterface $request) {
                    $decodedBody = \GuzzleHttp\json_decode($request->getBody()->getContents(), true);

                    $this->assertTrue($decodedBody['is_compatible']);
                }
            )->wait();

        $this->client
            ->sendAsync(checkSchemaCompatibilityAgainstVersionRequest(
                $this->incompatibleSchemaEvolution,
                self::SUBJECT_NAME,
                VERSION_LATEST
            ))->otherwise(
                function (RequestException $exception) {
                    $this->assertInstanceOf(
                        IncompatibleAvroSchemaException::class,
                        (ExceptionMap::instance())($exception)
                    );
                }
            )->wait();

        $this->client
            ->sendAsync(registerNewSchemaVersionWithSubjectRequest($this->invalidSchema, self::SUBJECT_NAME))
            ->otherwise(
                function (RequestException $exception) {
                    $this->assertInstanceOf(
                        InvalidAvroSchemaException::class,
                        (ExceptionMap::instance())($exception)
                    );
                }
            )->wait();

        $this->client
            ->sendAsync(singleSubjectVersionRequest('INVALID', VERSION_LATEST))
            ->otherwise(
                function (RequestException $exception) {
                    $this->assertInstanceOf(
                        SubjectNotFoundException::class,
                        (ExceptionMap::instance())($exception)
                    );
                }
            )->wait();

        $this->client
            ->sendAsync(singleSubjectVersionRequest(self::SUBJECT_NAME, 'INVALID'))
            ->otherwise(
                function (RequestException $exception) {
                    $this->assertInstanceOf(
                        InvalidVersionException::class,
                        (ExceptionMap::instance())($exception)
                    );
                }
            )->wait();

        $this->client
            ->sendAsync(singleSubjectVersionRequest(self::SUBJECT_NAME, '5'))
            ->otherwise(
                function (RequestException $exception) {
                    $this->assertInstanceOf(
                        VersionNotFoundException::class,
                        (ExceptionMap::instance())($exception)
                    );
                }
            )->wait();

        $this->client
            ->sendAsync(schemaRequest('6'))
            ->otherwise(
                function (RequestException $exception) {
                    $this->assertInstanceOf(
                        SchemaNotFoundException::class,
                        (ExceptionMap::instance())($exception)
                    );
                }
            )->wait();

        $this->client
            ->sendAsync(registerNewSchemaVersionWithSubjectRequest($this->compatibleSchemaEvolution, self::SUBJECT_NAME))
            ->then(
                function (ResponseInterface $request) {
                    $this->assertEquals(2, \GuzzleHttp\json_decode($request->getBody()->getContents(), true)['id']);
                }
            )->wait();

        $this->client
            ->sendAsync(allSubjectVersionsRequest(self::SUBJECT_NAME))
            ->then(
                function (ResponseInterface $request) {
                    $this->assertEquals([1, 2], \GuzzleHttp\json_decode($request->getBody()->getContents(), true));
                }
            )->wait();
    }

    /**
     * @test
     */
    public function managing_compatibility_levels()
    {
        $this->client
            ->sendAsync(defaultCompatibilityLevelRequest())
            ->then(
                function (ResponseInterface $request) {
                    $decodedBody = \GuzzleHttp\json_decode($request->getBody()->getContents(), true);

                    $this->assertEquals(
                        COMPATIBILITY_BACKWARD,
                        $decodedBody['compatibilityLevel']
                    );
                }
            )->wait();

        $this->client
            ->sendAsync(changeDefaultCompatibilityLevelRequest(COMPATIBILITY_FULL))
            ->then(
                function (ResponseInterface $request) {
                    $decodedBody = \GuzzleHttp\json_decode($request->getBody()->getContents(), true);

                    $this->assertEquals(
                        COMPATIBILITY_FULL,
                        $decodedBody['compatibility']
                    );
                }
            )->wait();

        $this->client
            ->sendAsync(changeSubjectCompatibilityLevelRequest(self::SUBJECT_NAME, COMPATIBILITY_FORWARD))
            ->then(
                function (ResponseInterface $request) {
                    $decodedBody = \GuzzleHttp\json_decode($request->getBody()->getContents(), true);

                    $this->assertEquals(
                        COMPATIBILITY_FORWARD,
                        $decodedBody['compatibility']
                    );
                }
            )->wait();

        $this->client
            ->sendAsync(subjectCompatibilityLevelRequest(self::SUBJECT_NAME))
            ->then(
                function (ResponseInterface $request) {
                    $decodedBody = \GuzzleHttp\json_decode($request->getBody()->getContents(), true);

                    $this->assertEquals(
                        COMPATIBILITY_FORWARD,
                        $decodedBody['compatibilityLevel']
                    );
                }
            )->wait();
    }
}
