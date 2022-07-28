<?php

declare(strict_types=1);

namespace FlixTech\SchemaRegistryApi\Exception;

class InvalidAvroSchemaException extends AbstractSchemaRegistryException
{
    const ERROR_CODE = 42201;
}
