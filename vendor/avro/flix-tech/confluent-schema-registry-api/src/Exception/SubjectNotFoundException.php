<?php

declare(strict_types=1);

namespace FlixTech\SchemaRegistryApi\Exception;

class SubjectNotFoundException extends AbstractSchemaRegistryException
{
    const ERROR_CODE = 40401;
}
