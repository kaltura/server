<?php

declare(strict_types=1);

namespace FlixTech\SchemaRegistryApi;

use AvroSchema;

/**
 * Client that talk to a schema registry over http
 *
 * See http://confluent.io/docs/current/schema-registry/docs/intro.html
 * See https://github.com/confluentinc/confluent-kafka-python
 */
interface Registry
{
    /**
     * Registers a given schema with a subject
     *
     * @param string        $subject
     * @param AvroSchema    $schema
     * @param callable|null $requestCallback
     *
     * @return mixed Should either return the schema id as int or a PromiseInterface
     *
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    public function register(string $subject, AvroSchema $schema, callable $requestCallback = null);

    /**
     * Look up the version of a schema for a given subject
     *
     * @param string        $subject
     * @param AvroSchema    $schema
     * @param callable|null $requestCallback
     *
     * @return mixed Should either return the version as int or a PromiseInterface
     *
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    public function schemaVersion(string $subject, AvroSchema $schema, callable $requestCallback = null);

    /**
     * Fetches the latest version of a schema from a subject
     *
     * @param string $subject
     * @param callable|null $requestCallback
     *
     * @return mixed Should either return the version as int or a PromiseInterface
     *
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    public function latestVersion(string $subject, callable $requestCallback = null);

    /**
     * Look up the global schema id of a schema for a given subject
     *
     * @param string        $subject
     * @param AvroSchema    $schema
     * @param callable|null $requestCallback
     *
     * @return mixed Should either return the schema id as int or a PromiseInterface
     *
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    public function schemaId(string $subject, AvroSchema $schema, callable $requestCallback = null);

    /**
     * Gets an AvroSchema for a given global schema id
     *
     * @param int           $schemaId
     * @param callable|null $requestCallback
     *
     * @return mixed Should either return the schema as AvroSchema or a PromiseInterface
     *
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    public function schemaForId(int $schemaId, callable $requestCallback = null);

    /**
     * Gets an AvroSchema for a given subject and version
     *
     * @param string        $subject
     * @param int           $version
     * @param callable|null $requestCallback
     *
     * @return mixed Should either return the schema as AvroSchema or a PromiseInterface
     *
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    public function schemaForSubjectAndVersion(string $subject, int $version, callable $requestCallback = null);
}
