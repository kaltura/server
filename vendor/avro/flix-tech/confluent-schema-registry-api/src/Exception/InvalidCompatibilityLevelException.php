<?php

declare(strict_types=1);

namespace FlixTech\SchemaRegistryApi\Exception;

class InvalidCompatibilityLevelException extends AbstractSchemaRegistryException
{
    const ERROR_CODE = 42203;
}
