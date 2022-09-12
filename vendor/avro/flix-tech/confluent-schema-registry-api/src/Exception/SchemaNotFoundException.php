<?php

declare(strict_types=1);

namespace FlixTech\SchemaRegistryApi\Exception;

class SchemaNotFoundException extends AbstractSchemaRegistryException
{
    const ERROR_CODE = 40403;
}
