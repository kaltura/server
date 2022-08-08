<?php

declare(strict_types=1);

namespace FlixTech\SchemaRegistryApi\Exception;

class IncompatibleAvroSchemaException extends AbstractSchemaRegistryException
{
    const ERROR_CODE = 409;
}
