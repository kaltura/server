<?php

declare(strict_types=1);

namespace FlixTech\SchemaRegistryApi;

use AvroSchema;

/**
 * {@inheritdoc}
 */
interface SynchronousRegistry extends Registry
{
    /**
     * {@inheritdoc}
     *
     * @return int The schema id of the registered AvroSchema
     */
    public function register(string $subject, AvroSchema $schema, callable $requestCallback = null): int;

    /**
     * {@inheritdoc}
     *
     * @return int The schema version of this AvroSchema for the given subject
     */
    public function schemaVersion(string $subject, AvroSchema $schema, callable $requestCallback = null): int;

    /**
     * {@inheritdoc}
     *
     * @return AvroSchema The latest schema for the given subject
     */
    public function latestVersion(string $subject, callable $requestCallback = null): AvroSchema;

    /**
     * {@inheritdoc}
     *
     * @return int The schema id of the registered AvroSchema
     */
    public function schemaId(string $subject, AvroSchema $schema, callable $requestCallback = null): int;

    /**
     * {@inheritdoc}
     *
     * @return AvroSchema The schema for the given schema id
     */
    public function schemaForId(int $schemaId, callable $requestCallback = null): AvroSchema;

    /**
     * {@inheritdoc}
     *
     * @return AvroSchema The schema for the given subject and version
     */
    public function schemaForSubjectAndVersion(string $subject, int $version, callable $requestCallback = null): AvroSchema;
}
