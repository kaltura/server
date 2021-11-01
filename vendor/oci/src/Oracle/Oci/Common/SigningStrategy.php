<?php

namespace Oracle\Oci\Common;

use InvalidArgumentException;

interface SigningStrategyInterface
{
    public function getRequiredSigningHeaders($verb); // : string[]
    public function getOptionalSigningHeaders(); // : string[]
    public function skipContentHeadersForStreamingPutRequests(); // : bool
}

class SigningStrategies
{
    public static function get($strategyName)
    {
        switch (strtolower($strategyName)) {
            case (string) StandardSigningStrategy::NAME: return StandardSigningStrategy::getSingleton();
            case (string) ExcludeBodySigningStrategy::NAME: return ExcludeBodySigningStrategy::getSingleton();
            case (string) ObjectStorageSigningStrategy::NAME: return ObjectStorageSigningStrategy::getSingleton();
            case (string) FederationSigningStrategy::NAME: return FederationSigningStrategy::getSingleton();
        }
        throw new InvalidArgumentException("Unknown signing strategy: $strategyName");
    }
}

class StandardSigningStrategy implements SigningStrategyInterface
{
    const NAME = "standard";
    public static $INSTANCE;
    public static function getSingleton()
    {
        if (StandardSigningStrategy::$INSTANCE == null) {
            StandardSigningStrategy::$INSTANCE = new StandardSigningStrategy();
        }
        return StandardSigningStrategy::$INSTANCE;
    }

    private static $requiredHeadersToSign;
    private static $optionalSigningHeaders;

    public function __construct()
    {
        if (StandardSigningStrategy::$requiredHeadersToSign == null) {
            StandardSigningStrategy::$requiredHeadersToSign = SigningStrategyConstants::requiredHeadersToSign();
        }
        if (StandardSigningStrategy::$optionalSigningHeaders == null) {
            StandardSigningStrategy::$optionalSigningHeaders = SigningStrategyConstants::OPTIONAL_SIGNING_HEADERS;
        }
    }

    public function getRequiredSigningHeaders($verb) // : string[]
    {
        return StandardSigningStrategy::$requiredHeadersToSign->getHeaders(strtolower($verb));
    }

    public function getOptionalSigningHeaders() // : string[]
    {
        return StandardSigningStrategy::$optionalSigningHeaders;
    }

    public function skipContentHeadersForStreamingPutRequests() // : bool
    {
        return false;
    }

    public function __toString()
    {
        return StandardSigningStrategy::NAME;
    }
}

class ExcludeBodySigningStrategy implements SigningStrategyInterface
{
    const NAME = "exclude_body";
    public static $INSTANCE;
    public static function getSingleton()
    {
        if (ExcludeBodySigningStrategy::$INSTANCE == null) {
            ExcludeBodySigningStrategy::$INSTANCE = new ExcludeBodySigningStrategy();
        }
        return ExcludeBodySigningStrategy::$INSTANCE;
    }

    private static $requiredHeadersToSign;
    private static $optionalSigningHeaders;

    public function __construct()
    {
        if (ExcludeBodySigningStrategy::$requiredHeadersToSign == null) {
            ExcludeBodySigningStrategy::$requiredHeadersToSign = SigningStrategyConstants::requiredExcludeBodyHeadersToSign();
        }
        if (ExcludeBodySigningStrategy::$optionalSigningHeaders == null) {
            ExcludeBodySigningStrategy::$optionalSigningHeaders = SigningStrategyConstants::OPTIONAL_SIGNING_HEADERS;
        }
    }

    public function getRequiredSigningHeaders($verb) // : string[]
    {
        return ExcludeBodySigningStrategy::$requiredHeadersToSign->getHeaders(strtolower($verb));
    }

    public function getOptionalSigningHeaders() // : string[]
    {
        return ExcludeBodySigningStrategy::$optionalSigningHeaders;
    }

    public function skipContentHeadersForStreamingPutRequests() // : bool
    {
        return true;
    }

    public function __toString()
    {
        return ExcludeBodySigningStrategy::NAME;
    }
}

class ObjectStorageSigningStrategy implements SigningStrategyInterface
{
    const NAME = "object_storage";
    public static $INSTANCE;
    public static function getSingleton()
    {
        if (ObjectStorageSigningStrategy::$INSTANCE == null) {
            ObjectStorageSigningStrategy::$INSTANCE = new ObjectStorageSigningStrategy();
        }
        return ObjectStorageSigningStrategy::$INSTANCE;
    }

    private static $requiredHeadersToSign;
    private static $optionalSigningHeaders;

    public function __construct()
    {
        if (ObjectStorageSigningStrategy::$requiredHeadersToSign == null) {
            ObjectStorageSigningStrategy::$requiredHeadersToSign = SigningStrategyConstants::objectStorageHeadersToSign();
        }
        if (ObjectStorageSigningStrategy::$optionalSigningHeaders == null) {
            ObjectStorageSigningStrategy::$optionalSigningHeaders = SigningStrategyConstants::OPTIONAL_SIGNING_HEADERS;
        }
    }

    public function getRequiredSigningHeaders($verb) // : string[]
    {
        return ObjectStorageSigningStrategy::$requiredHeadersToSign->getHeaders(strtolower($verb));
    }

    public function getOptionalSigningHeaders() // : string[]
    {
        return ObjectStorageSigningStrategy::$optionalSigningHeaders;
    }

    public function skipContentHeadersForStreamingPutRequests() // : bool
    {
        return true;
    }

    public function __toString()
    {
        return ObjectStorageSigningStrategy::NAME;
    }
}

class FederationSigningStrategy implements SigningStrategyInterface
{
    const NAME = "federation";
    public static $INSTANCE;
    public static function getSingleton()
    {
        if (FederationSigningStrategy::$INSTANCE == null) {
            FederationSigningStrategy::$INSTANCE = new FederationSigningStrategy();
        }
        return FederationSigningStrategy::$INSTANCE;
    }

    private static $requiredHeadersToSign;
    private static $optionalSigningHeaders;

    public function __construct()
    {
        if (FederationSigningStrategy::$requiredHeadersToSign == null) {
            FederationSigningStrategy::$requiredHeadersToSign = SigningStrategyConstants::objectStorageHeadersToSign();
        }
        if (FederationSigningStrategy::$optionalSigningHeaders == null) {
            FederationSigningStrategy::$optionalSigningHeaders = SigningStrategyConstants::OPTIONAL_SIGNING_HEADERS;
        }
    }

    public function getRequiredSigningHeaders($verb) // : string[]
    {
        return SigningStrategyConstants::federationRemoveHostHeader(FederationSigningStrategy::$requiredHeadersToSign->getHeaders(strtolower($verb)));
    }

    public function getOptionalSigningHeaders() // : string[]
    {
        return FederationSigningStrategy::$optionalSigningHeaders;
    }

    public function skipContentHeadersForStreamingPutRequests() // : bool
    {
        return true;
    }

    public function __toString()
    {
        return FederationSigningStrategy::NAME;
    }
}

class HeadersToSign
{
    private $verbToHeaders;

    public function __construct($verbToHeaders)
    {
        $this->verbToHeaders = $verbToHeaders;
    }

    public function getVerbToHeaders()
    {
        return $this->verbToHeaders;
    }

    public function getHeaders($verb)
    {
        return $this->verbToHeaders[$verb];
    }
}

class SigningStrategyConstants
{
    public static function requiredHeadersToSign()
    {
        return new HeadersToSign([
            "get" => SigningStrategyConstants::GENERAL_SIGNING_HEADERS,
            "head" => SigningStrategyConstants::GENERAL_SIGNING_HEADERS,
            "delete" => SigningStrategyConstants::GENERAL_SIGNING_HEADERS,
            "put" => SigningStrategyConstants::allSigningHeaders(),
            "post" => SigningStrategyConstants::allSigningHeaders(),
            "patch" => SigningStrategyConstants::allSigningHeaders()
        ]);
    }

    public static function objectStorageHeadersToSign()
    {
        return new HeadersToSign([
            "get" => SigningStrategyConstants::GENERAL_SIGNING_HEADERS,
            "head" => SigningStrategyConstants::GENERAL_SIGNING_HEADERS,
            "delete" => SigningStrategyConstants::GENERAL_SIGNING_HEADERS,
            "put" => SigningStrategyConstants::GENERAL_SIGNING_HEADERS, // PUT is a special case for Object Storage
            "post" => SigningStrategyConstants::allSigningHeaders(),
            "patch" => SigningStrategyConstants::allSigningHeaders()
        ]);
    }

    public static function requiredExcludeBodyHeadersToSign()
    {
        return new HeadersToSign([
            "get" => SigningStrategyConstants::GENERAL_SIGNING_HEADERS,
            "head" => SigningStrategyConstants::GENERAL_SIGNING_HEADERS,
            "delete" => SigningStrategyConstants::GENERAL_SIGNING_HEADERS,
            "put" => SigningStrategyConstants::GENERAL_SIGNING_HEADERS,
            "post" => SigningStrategyConstants::GENERAL_SIGNING_HEADERS,
            "patch" => SigningStrategyConstants::GENERAL_SIGNING_HEADERS
        ]);
    }

    const GENERAL_SIGNING_HEADERS = [Constants::DATE_HEADER_NAME, Constants::REQUEST_TARGET_HEADER_NAME, Constants::HOST_HEADER_NAME];
    const BODY_SIGNING_HEADERS = [Constants::CONTENT_LENGTH_HEADER_NAME, Constants::CONTENT_TYPE_HEADER_NAME, Constants::X_CONTENT_SHA256_HEADER_NAME];

    public static function allSigningHeaders()
    {
        // starting with PHP 7.4, we could declare another constant instead:
        // const ALL_SIGNING_HEADERS = [...SigningStrategyConstants::GENERAL_SIGNING_HEADERS, ...SigningStrategyConstants::BODY_SIGNING_HEADERS];
        return array_merge(SigningStrategyConstants::GENERAL_SIGNING_HEADERS, SigningStrategyConstants::BODY_SIGNING_HEADERS);
    }

    public static function federationRemoveHostHeader($headers)
    {
        if (($key = array_search(Constants::HOST_HEADER_NAME, $headers)) !== false) {
            unset($headers[$key]);
        }
        return $headers;
    }

    const OPTIONAL_SIGNING_HEADERS = [Constants::X_CROSS_TENANCY_REQUEST_HEADER_NAME, Constants::X_SUBSCRIPTION_HEADER_NAME, Constants::OPC_OBO_TOKEN_HEADER_NAME];
}
