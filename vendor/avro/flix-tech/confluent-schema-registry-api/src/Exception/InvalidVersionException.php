<?php

declare(strict_types=1);

namespace FlixTech\SchemaRegistryApi\Exception;

class InvalidVersionException extends AbstractSchemaRegistryException
{
    const ERROR_CODE = 42202;
}
