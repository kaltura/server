<?php

declare(strict_types=1);

namespace FlixTech\SchemaRegistryApi\Exception;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

final class ExceptionMap
{
    const UNKNOWN_ERROR_MESSAGE = 'Unknown Error';
    const ERROR_CODE_FIELD_NAME = 'error_code';
    const ERROR_MESSAGE_FIELD_NAME = 'message';

    /**
     * @var \FlixTech\SchemaRegistryApi\Exception\ExceptionMap
     */
    private static $instance;

    public static function instance(): ExceptionMap
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
    }

    /**
     * Maps a RequestException to the internal SchemaRegistryException types.
     *
     * @param RequestException $exception
     *
     * @return SchemaRegistryException
     *
     * @throws \RuntimeException
     */
    public function __invoke(RequestException $exception): SchemaRegistryException
    {
        $response = $this->guardAgainstMissingResponse($exception);
        $decodedBody = $this->guardAgainstMissingErrorCode($response);
        $errorCode = $decodedBody[self::ERROR_CODE_FIELD_NAME];
        $errorMessage = $decodedBody[self::ERROR_MESSAGE_FIELD_NAME];

        return $this->mapErrorCodeToException($errorCode, $errorMessage);
    }

    private function guardAgainstMissingResponse(RequestException $exception): ResponseInterface
    {
        $response = $exception->getResponse();

        if (!$response) {
            throw new \RuntimeException('RequestException has no response to inspect', 0, $exception);
        }

        return $response;
    }

    private function guardAgainstMissingErrorCode(ResponseInterface $response): array
    {
        try {
            $decodedBody = \GuzzleHttp\json_decode((string) $response->getBody(), true);

            if (!\array_key_exists(self::ERROR_CODE_FIELD_NAME, $decodedBody)) {
                throw new \RuntimeException(
                    sprintf(
                        'Invalid message body received - cannot find "error_code" field in response body "%s"',
                        (string) $response->getBody()
                    )
                );
            }

            return $decodedBody;
        } catch (\Exception $e) {
            throw new \RuntimeException(
                \sprintf(
                    'Invalid message body received - cannot find "error_code" field in response body "%s"',
                    (string) $response->getBody()
                ),
                $e->getCode(),
                $e
            );
        }
    }

    private function mapErrorCodeToException($errorCode, $errorMessage)
    {
        switch ($errorCode) {
            case IncompatibleAvroSchemaException::errorCode():
                return new IncompatibleAvroSchemaException($errorMessage, $errorCode);

            case BackendDataStoreException::errorCode():
                return new BackendDataStoreException($errorMessage, $errorCode);

            case OperationTimedOutException::errorCode():
                return new OperationTimedOutException($errorMessage, $errorCode);

            case MasterProxyException::errorCode():
                return new MasterProxyException($errorMessage, $errorCode);

            case InvalidVersionException::errorCode():
                return new InvalidVersionException($errorMessage, $errorCode);

            case InvalidAvroSchemaException::errorCode():
                return new InvalidAvroSchemaException($errorMessage, $errorCode);

            case SchemaNotFoundException::errorCode():
                return new SchemaNotFoundException($errorMessage, $errorCode);

            case SubjectNotFoundException::errorCode():
                return new SubjectNotFoundException($errorMessage, $errorCode);

            case VersionNotFoundException::errorCode():
                return new VersionNotFoundException($errorMessage, $errorCode);

            case InvalidCompatibilityLevelException::errorCode():
                return new InvalidCompatibilityLevelException($errorMessage, $errorCode);

            default:
                throw new \RuntimeException(sprintf('Unknown error code "%d"', $errorCode));
        }
    }
}
