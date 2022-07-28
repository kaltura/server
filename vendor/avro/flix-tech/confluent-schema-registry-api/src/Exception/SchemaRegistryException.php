<?php

declare(strict_types=1);

namespace FlixTech\SchemaRegistryApi\Exception;

interface SchemaRegistryException extends \Throwable
{
    public static function errorCode(): int;
}
