<?php

declare(strict_types=1);

namespace FlixTech\SchemaRegistryApi\Registry;

use Assert\Assert;
use Psr\Http\Message\RequestInterface;
use TRex\Reflection\CallableReflection;

/**
 * Validates if a given request callback matches the specs needed for it to work.
 * It uses reflection to inspect the parameters and return types of callables passed in.
 */
class RequestCallbackValidator
{
    /**
     * @var RequestCallbackValidator
     */
    private static $instance;

    public static function instance(): RequestCallbackValidator
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param callable|null $requestCallback
     *
     * @return callable|null
     *
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function __invoke(callable $requestCallback = null)
    {
        if (!$requestCallback) {
            return $requestCallback;
        }

        $reflection = (new CallableReflection($requestCallback))->getReflector();

        Assert::that($reflection->getNumberOfParameters())
            ->greaterOrEqualThan(
                1,
                sprintf('There must be at least one callback parameter that implements "%s".', RequestInterface::class)
            );

        $reflectionParameter = $reflection->getParameters()[0];

        if (!$reflectionParameter->hasType()) {
            throw new \InvalidArgumentException(
                sprintf(
                    'First parameter of callback must be type hinted against "%s" or classes that implement it.',
                    RequestInterface::class
                )
            );
        }

        Assert::that((string) $reflectionParameter->getType())
            ->implementsInterface(
                RequestInterface::class,
                'First parameter of type "%s" does not implement "%s".'
            );

        if ($reflection->hasReturnType()) {
            Assert::that((string) $reflection->getReturnType())
                ->implementsInterface(
                    RequestInterface::class,
                    'Return type "%s" of request callback does not implement interface "%s".'
                );
        }

        return $requestCallback;
    }
}
