<?php

declare(strict_types=1);

namespace FlixTech\SchemaRegistryApi\Exception;

class VersionNotFoundException extends AbstractSchemaRegistryException
{
    const ERROR_CODE = 40402;
}
