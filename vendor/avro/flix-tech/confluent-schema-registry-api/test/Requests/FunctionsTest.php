<?php

declare(strict_types=1);

namespace FlixTech\SchemaRegistryApi\Test\Requests;

use PHPUnit\Framework\TestCase;
use const FlixTech\SchemaRegistryApi\Constants\COMPATIBILITY_BACKWARD;
use const FlixTech\SchemaRegistryApi\Constants\COMPATIBILITY_FORWARD;
use const FlixTech\SchemaRegistryApi\Constants\COMPATIBILITY_FULL;
use const FlixTech\SchemaRegistryApi\Constants\COMPATIBILITY_NONE;
use const FlixTech\SchemaRegistryApi\Constants\VERSION_LATEST;
use function FlixTech\SchemaRegistryApi\Requests\allSubjectsRequest;
use function FlixTech\SchemaRegistryApi\Requests\allSubjectVersionsRequest;
use function FlixTech\SchemaRegistryApi\Requests\changeDefaultCompatibilityLevelRequest;
use function FlixTech\SchemaRegistryApi\Requests\changeSubjectCompatibilityLevelRequest;
use function FlixTech\SchemaRegistryApi\Requests\checkIfSubjectHasSchemaRegisteredRequest;
use function FlixTech\SchemaRegistryApi\Requests\checkSchemaCompatibilityAgainstVersionRequest;
use function FlixTech\SchemaRegistryApi\Requests\defaultCompatibilityLevelRequest;
use function FlixTech\SchemaRegistryApi\Requests\deleteSubjectRequest;
use function FlixTech\SchemaRegistryApi\Requests\deleteSubjectVersionRequest;
use function FlixTech\SchemaRegistryApi\Requests\prepareCompatibilityLevelForTransport;
use function FlixTech\SchemaRegistryApi\Requests\prepareJsonSchemaForTransfer;
use function FlixTech\SchemaRegistryApi\Requests\registerNewSchemaVersionWithSubjectRequest;
use function FlixTech\SchemaRegistryApi\Requests\schemaRequest;
use function FlixTech\SchemaRegistryApi\Requests\singleSubjectVersionRequest;
use function FlixTech\SchemaRegistryApi\Requests\subjectCompatibilityLevelRequest;
use function FlixTech\SchemaRegistryApi\Requests\validateCompatibilityLevel;
use function FlixTech\SchemaRegistryApi\Requests\validateSchemaId;
use function FlixTech\SchemaRegistryApi\Requests\validateSchemaStringAsJson;
use function FlixTech\SchemaRegistryApi\Requests\validateVersionId;

class FunctionsTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_produce_a_Request_to_get_all_subjects()
    {
        $request = allSubjectsRequest();

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/subjects', $request->getUri());
        $this->assertEquals(['application/vnd.schemaregistry.v1+json'], $request->getHeader('Accept'));
    }

    /**
     * @test
     */
    public function it_should_produce_a_Request_to_get_all_subject_versions()
    {
        $request = allSubjectVersionsRequest('test');

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/subjects/test/versions', $request->getUri());
        $this->assertEquals(['application/vnd.schemaregistry.v1+json'], $request->getHeader('Accept'));
    }

    /**
     * @test
     */
    public function it_should_produce_a_Request_to_get_a_specific_subject_version()
    {
        $request = singleSubjectVersionRequest('test', '3');

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/subjects/test/versions/3', $request->getUri());
        $this->assertEquals(['application/vnd.schemaregistry.v1+json'], $request->getHeader('Accept'));
    }

    /**
     * @test
     */
    public function it_should_produce_a_request_to_register_a_new_schema_version()
    {
        $request = registerNewSchemaVersionWithSubjectRequest('{"type": "string"}', 'test');

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/subjects/test/versions', $request->getUri());
        $this->assertEquals(['application/vnd.schemaregistry.v1+json'], $request->getHeader('Accept'));
        $this->assertEquals('{"schema":"{\"type\":\"string\"}"}', $request->getBody()->getContents());

        $request = registerNewSchemaVersionWithSubjectRequest('{"schema": "{\"type\": \"string\"}"}', 'test');

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/subjects/test/versions', $request->getUri());
        $this->assertEquals(['application/vnd.schemaregistry.v1+json'], $request->getHeader('Accept'));
        $this->assertEquals('{"schema":"{\"type\": \"string\"}"}', $request->getBody()->getContents());
    }

    /**
     * @test
     */
    public function it_should_produce_a_request_to_check_schema_compatibility_against_a_subject_version()
    {
        $request = checkSchemaCompatibilityAgainstVersionRequest(
            '{"type":"test"}',
            'test',
            VERSION_LATEST
        );

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/compatibility/subjects/test/versions/latest', $request->getUri());
        $this->assertEquals('{"schema":"{\"type\":\"test\"}"}', $request->getBody()->getContents());
        $this->assertEquals(['application/vnd.schemaregistry.v1+json'], $request->getHeader('Accept'));
    }

    /**
     * @test
     */
    public function it_should_produce_a_request_to_check_if_a_subject_already_has_a_schema()
    {
        $request = checkIfSubjectHasSchemaRegisteredRequest('test', '{"type":"test"}');

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/subjects/test', $request->getUri());
        $this->assertEquals('{"schema":"{\"type\":\"test\"}"}', $request->getBody()->getContents());
        $this->assertEquals(['application/vnd.schemaregistry.v1+json'], $request->getHeader('Accept'));
    }

    /**
     * @test
     */
    public function it_should_produce_a_request_to_get_a_specific_schema_by_id()
    {
        $request = schemaRequest('3');

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/schemas/ids/3', $request->getUri());
        $this->assertEquals(['application/vnd.schemaregistry.v1+json'], $request->getHeader('Accept'));
    }

    /**
     * @test
     */
    public function it_should_produce_a_request_to_get_the_global_compatibility_level()
    {
        $request = defaultCompatibilityLevelRequest();

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/config', $request->getUri());
        $this->assertEquals(['application/vnd.schemaregistry.v1+json'], $request->getHeader('Accept'));
    }

    /**
     * @test
     */
    public function it_should_produce_a_request_to_change_the_global_compatibility_level()
    {
        $request = changeDefaultCompatibilityLevelRequest(COMPATIBILITY_FULL);

        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('/config', $request->getUri());
        $this->assertEquals('{"compatibility":"FULL"}', $request->getBody()->getContents());
        $this->assertEquals(['application/vnd.schemaregistry.v1+json'], $request->getHeader('Accept'));
    }

    /**
     * @test
     */
    public function it_should_produce_a_request_to_get_the_subject_compatibility_level()
    {
        $request = subjectCompatibilityLevelRequest('test');

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/config/test', $request->getUri());
        $this->assertEquals(['application/vnd.schemaregistry.v1+json'], $request->getHeader('Accept'));
    }

    /**
     * @test
     */
    public function it_should_produce_a_request_to_change_the_subject_compatibility_level()
    {
        $request = changeSubjectCompatibilityLevelRequest('test', COMPATIBILITY_FORWARD);

        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('/config/test', $request->getUri());
        $this->assertEquals('{"compatibility":"FORWARD"}', $request->getBody()->getContents());
        $this->assertEquals(['application/vnd.schemaregistry.v1+json'], $request->getHeader('Accept'));
    }

    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $schema must be a valid JSON string
     */
    public function it_should_validate_a_JSON_schema_string()
    {
        $this->assertJsonStringEqualsJsonString('{"type":"test"}', validateSchemaStringAsJson('{"type":"test"}'));

        validateSchemaStringAsJson('INVALID');
    }

    /**
     * @test
     */
    public function it_should_prepare_a_JSON_schema_for_transfer()
    {
        $this->assertJsonStringEqualsJsonString(
            '{"schema":"{\"type\":\"string\"}"}',
            prepareJsonSchemaForTransfer('{"type": "string"}')
        );

        $this->assertJsonStringEqualsJsonString(
            '{"schema":"{\"type\": \"string\"}"}',
            prepareJsonSchemaForTransfer('{"schema":"{\"type\": \"string\"}"}')
        );
    }

    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $level must be one of "NONE", "BACKWARD", "FORWARD" or "FULL"
     */
    public function it_should_validate_a_compatibility_level_string()
    {
        $this->assertEquals(COMPATIBILITY_NONE, validateCompatibilityLevel(COMPATIBILITY_NONE));
        $this->assertEquals(COMPATIBILITY_FULL, validateCompatibilityLevel(COMPATIBILITY_FULL));
        $this->assertEquals(COMPATIBILITY_BACKWARD, validateCompatibilityLevel(COMPATIBILITY_BACKWARD));
        $this->assertEquals(COMPATIBILITY_FORWARD, validateCompatibilityLevel(COMPATIBILITY_FORWARD));

        validateCompatibilityLevel('INVALID');
    }

    /**
     * @test
     */
    public function it_should_prepare_compatibility_string_for_transport()
    {
        $this->assertEquals('{"compatibility":"NONE"}', prepareCompatibilityLevelForTransport(COMPATIBILITY_NONE));
        $this->assertEquals('{"compatibility":"BACKWARD"}', prepareCompatibilityLevelForTransport(COMPATIBILITY_BACKWARD));
        $this->assertEquals('{"compatibility":"FORWARD"}', prepareCompatibilityLevelForTransport(COMPATIBILITY_FORWARD));
        $this->assertEquals('{"compatibility":"FULL"}', prepareCompatibilityLevelForTransport(COMPATIBILITY_FULL));
    }


    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $versionId must be an integer of type int or string
     */
    public function it_should_validate_version_id_type()
    {
        validateVersionId([3]);
    }

    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $versionId must be between 1 and 2^31 - 1
     */
    public function it_should_validate_version_id_overflow()
    {
        validateVersionId(2 ** 31);
    }

    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $versionId must be between 1 and 2^31 - 1
     */
    public function it_should_validate_version_id_less_than_one()
    {
        validateVersionId(0);
    }

    /**
     * @test
     */
    public function it_should_validate_valid_version_id()
    {
        $this->assertSame(VERSION_LATEST, validateVersionId(VERSION_LATEST));
        $this->assertSame('3', validateVersionId(3));
        $this->assertSame('3', validateVersionId('3'));
    }

    /**
     * @test
     */
    public function it_should_validate_valid_schema_ids()
    {
        $this->assertSame('3', validateSchemaId(3));
        $this->assertSame('3', validateSchemaId('3'));
    }

    /**
     * @test
     */
    public function it_should_produce_a_valid_subject_deletion_request()
    {
        $request = deleteSubjectRequest('test');

        $this->assertEquals('DELETE', $request->getMethod());
        $this->assertEquals('/subjects/test', $request->getUri());
        $this->assertEquals(['application/vnd.schemaregistry.v1+json'], $request->getHeader('Accept'));
    }

    /**
     * @test
     */
    public function it_should_produce_a_valid_subject_version_deletion_request()
    {
        $request = deleteSubjectVersionRequest('test', VERSION_LATEST);

        $this->assertEquals('DELETE', $request->getMethod());
        $this->assertEquals('/subjects/test/versions/latest', $request->getUri());
        $this->assertEquals(['application/vnd.schemaregistry.v1+json'], $request->getHeader('Accept'));

        $request = deleteSubjectVersionRequest('test', '5');

        $this->assertEquals('DELETE', $request->getMethod());
        $this->assertEquals('/subjects/test/versions/5', $request->getUri());
        $this->assertEquals(['application/vnd.schemaregistry.v1+json'], $request->getHeader('Accept'));
    }
}
