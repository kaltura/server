<?php

declare(strict_types=1);

namespace FlixTech\SchemaRegistryApi;

use AvroSchema;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * {@inheritdoc}
 */
interface AsynchronousRegistry extends Registry
{
    /**
     * {@inheritdoc}
     *
     * @return PromiseInterface Either the schema id as int or a SchemaRegistryException object when fulfilled
     */
    public function register(string $subject, AvroSchema $schema, callable $requestCallback = null): PromiseInterface;

    /**
     * {@inheritdoc}
     *
     * @return PromiseInterface Either the version as int or a SchemaRegistryException object when fulfilled
     */
    public function schemaVersion(string $subject, AvroSchema $schema, callable $requestCallback = null): PromiseInterface;

    /**
     * {@inheritdoc}
     *
     * @return PromiseInterface Either the schema as AvroSchema or a SchemaRegistryException object when fulfilled
     */
    public function latestVersion(string $subject, callable $requestCallback = null): PromiseInterface;

    /**
     * {@inheritdoc}
     *
     * @return PromiseInterface Either the schema id as int or a SchemaRegistryException object when fulfilled
     */
    public function schemaId(string $subject, AvroSchema $schema, callable $requestCallback = null): PromiseInterface;

    /**
     * {@inheritdoc}
     *
     * @return PromiseInterface Either the schema as AvroSchema or a SchemaRegistryException object when fulfilled
     */
    public function schemaForId(int $schemaId, callable $requestCallback = null): PromiseInterface;

    /**
     * {@inheritdoc}
     *
     * @return PromiseInterface Either the schema as AvroSchema or a SchemaRegistryException object when fulfilled
     */
    public function schemaForSubjectAndVersion(string $subject, int $version, callable $requestCallback = null): PromiseInterface;
}
