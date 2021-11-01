<?php

namespace Oracle\Oci\Common;

use Psr\Http\Message\RequestInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use InvalidArgumentException;
use OpenSSLAsymmetricKey;
use Oracle\Oci\Common\Auth\AuthProviderInterface;
use Oracle\Oci\Common\Auth\RegionProviderInterface;
use Oracle\Oci\Common\Logging\LogAdapterInterface;
use Oracle\Oci\Common\Logging\Logger;
use Oracle\Oci\Common\Logging\NamedLogAdapterDecorator;
use Oracle\Oci\Common\Logging\RedactingLogAdapterDecorator;

abstract class AbstractClient
{
    /*LogAdapterInterface*/ protected $logAdapter;

    /*AuthProviderInterface*/ protected $auth_provider;
    /*?Region*/ protected $region;
    /*SigningStrategyInterface*/ protected $signingStrategy;

    /*string*/ protected $endpoint;
    protected $client;

    const DEFAULT_HEADERS = [];

    public function __construct(
        $endpointTemplate,
        AuthProviderInterface $auth_provider,
        SigningStrategyInterface $signingStrategy,
        $region=null,
        $endpoint=null
    ) {
        $this->auth_provider = $auth_provider;
        $this->signingStrategy = $signingStrategy;

        $this->region = AbstractClient::determineRegion($region, $auth_provider, $this->logger());
        $this->endpoint = AbstractClient::determineEndpoint($endpoint, $this->region, $endpointTemplate, $this->logger());

        $handler = new CurlHandler();
        $stack = HandlerStack::create($handler);

        // place signing middleware after prepare-body so it can access Content-Length header
        $stack->after('prepare_body', Middleware::mapRequest(function (RequestInterface $request) {
            return $this->signingMiddleware($request);
        }));

        $this->client = new Client([
            'handler' => $stack
        ]);
    }

    /**
     * Return the chosen region; or a new region in an the unknown regions realm; or null, if no region was selected.
     *
     * If both a region is specified and the auth provider provides a region, the region specified directly takes precedence.
     *
     * @param region the Region object; or region id or region code as a string
     * @param auth_provider the auth provider; if it implements RegionProviderInterface, the auth provider may provide the region
     * @param logger logger for informational messages
     *
     * @return Region region or null
     */
    public static function determineRegion(/*Region|string*/ $region, AuthProviderInterface $auth_provider, LogAdapterInterface $logger) // : Region
    {
        $resultRegion = null;

        if ($auth_provider instanceof RegionProviderInterface) {
            $resultRegion = $auth_provider->getRegion();
            if (!$resultRegion instanceof Region) {
                throw new InvalidArgumentException(
                    "The region returned by RegionProviderInterface must be an Oracle\Oci\Common\Region, " .
                    "but was " . StringUtils::get_type_or_class($resultRegion) . "."
                );
            }
        }
        if ($region != null) {
            if ($region instanceof Region) {
                $resultRegion = $region;
            } else {
                $knownRegion = Region::getRegion($region);
                if ($knownRegion == null) {
                    // forward-compatibility for unknown regions
                    $realm = Realm::getRealmForUnknownRegion();

                    $resultRegion = new Region($region, $region, $realm);
                    $logger->info("Region $region is unknown, assuming it to be in realm $realm. Registered $resultRegion.");
                } else {
                    $resultRegion = $knownRegion;
                }
            }
        }

        return $resultRegion;
    }

    /**
     * Return the endpoint, if specified direction; or construct the endpoint based on the region and the endpoint template.
     *
     * @param endpoint (may be null) if not null, this will be used as endpoint
     * @param region (may be null) if not null, will be used to construct the endpoint, unless an endpoint is set directly
     * @param endpointTemplate endpoint template to construct an endpoint from a region
     * @param logger logger for debug messages
     *
     * @return string endpoint
     */
    public static function determineEndpoint(/*?string*/ $endpoint, /*?Region*/ $region, /*string*/ $endpointTemplate, LogAdapterInterface $logger) // : string
    {
        if ($region == null && $endpoint == null) {
            // Region still hasn't been set, and we don't have an endpoint either.
            throw new InvalidArgumentException('Neither region nor endpoint is set.');
        }

        $resultEndpoint = null;
        if ($endpoint != null) {
            $resultEndpoint = $endpoint;
        } else {
            if (!($region instanceof Region)) {
                throw new InvalidArgumentException(
                    "The region must be an Oracle\Oci\Common\Region, but was " . StringUtils::get_type_or_class($region) . "."
                );
            }
            $resultEndpoint = str_replace('{region}', $region->getRegionId(), $endpointTemplate);
            $resultEndpoint = str_replace('{secondLevelDomain}', $region->getRealm()->getRealmDomainComponent(), $resultEndpoint);
        }
        $logger->debug("Final endpoint: $resultEndpoint");

        return $resultEndpoint;
    }

    protected function signingMiddleware(RequestInterface $request)
    {
        $middlewareLogger = $this->logger("middleware");
        $middlewareLogger->debug("Request URI: " . $request->getUri(), "uri");

        $signingStrategyForOperation = $this->signingStrategy;
        if ($request->hasHeader(Constants::PER_OPERATION_SIGNING_STRATEGY_NAME_HEADER_NAME)) {
            $perOperationSigningStrategy = $request->getHeader(Constants::PER_OPERATION_SIGNING_STRATEGY_NAME_HEADER_NAME);
            $c = count($perOperationSigningStrategy);
            if ($c == 1) {
                $signingStrategyForOperation = SigningStrategies::get($perOperationSigningStrategy[0]);
                $request = $request->withoutHeader(Constants::PER_OPERATION_SIGNING_STRATEGY_NAME_HEADER_NAME);
                $middlewareLogger->debug("Using per-operation signing strategy '$signingStrategyForOperation'.", "signing\\strategy");
            } elseif ($c > 1) {
                throw new InvalidArgumentException("Should only have one value for the " . Constants::PER_OPERATION_SIGNING_STRATEGY_NAME_HEADER_NAME . " header, had $c.");
            }
        } else {
            $middlewareLogger->debug("Using per-client signing strategy '$signingStrategyForOperation'.", "signing\\strategy");
        }

        // headers required for signing
        $method = strtolower($request->getMethod());
        $headers = $signingStrategyForOperation->getRequiredSigningHeaders($method);
        $middlewareLogger->debug("Required headers: '" . implode(" ", $headers) . "'.", "signing\\strategy\\verbose");
        $optionalHeaders = $signingStrategyForOperation->getOptionalSigningHeaders();
        $middlewareLogger->debug("Optional headers: '" . implode(" ", $optionalHeaders) . "'.", "signing\\strategy\\verbose");
        foreach ($optionalHeaders as $oh) {
            if ($request->hasHeader($oh)) {
                $headers[] = $oh;
            }
        }
        $headersString = implode(" ", $headers);
        $middlewareLogger->debug("Headers used for signing: '$headersString'.", "signing\\strategy\\verbose");

        $signingParts = [];
        foreach ($headers as $h) {
            $lch = strtolower($h);
            switch ($lch) {
            case strtolower(Constants::DATE_HEADER_NAME):
                // example: Thu, 05 Jan 2014 21:31:40 GMT
                $date = gmdate("D, d M Y H:i:s T", time());
                $signingParts[] = "$lch: $date";
                $request = $request->withHeader($h, $date);
                break;
            case strtolower(Constants::REQUEST_TARGET_HEADER_NAME):
                $request_target = $request->getRequestTarget();
                $signingParts[] = "$lch: $method $request_target";
                break;
            case strtolower(Constants::HOST_HEADER_NAME):
                $host = $request->getHeader($h)[0];
                $signingParts[] = "$lch: $host";
                break;
            case strtolower(Constants::CONTENT_LENGTH_HEADER_NAME):
                $clHeaders = $request->getHeader(Constants::CONTENT_LENGTH_HEADER_NAME);
                if ($clHeaders != null && count($clHeaders) > 0) {
                    $content_length = $clHeaders[0];
                } else {
                    // if content length is 0 we still need to explicitly send the Content-Length header
                    $content_length = 0;
                    $request = $request->withHeader(Constants::CONTENT_LENGTH_HEADER_NAME, 0);
                }
                $signingParts[] = "$lch: $content_length";
                break;
            case strtolower(Constants::CONTENT_TYPE_HEADER_NAME):
                $content_type = $request->getHeader(Constants::CONTENT_TYPE_HEADER_NAME)[0];
                $signingParts[] = "$lch: $content_type";
                break;
            case strtolower(Constants::X_CONTENT_SHA256_HEADER_NAME):
                $content_sha256 = base64_encode(hex2bin(hash("sha256", $request->getBody())));
                $signingParts[] = "$lch: $content_sha256";
                $request = $request->withHeader(Constants::X_CONTENT_SHA256_HEADER_NAME, $content_sha256);
                break;
            default:
                $optHeaders = $request->getHeader($h);
                if ($optHeaders != null) {
                    $c = count($optHeaders);
                    if ($c == 1) {
                        $optVal = $optHeaders[0];
                        $signingParts[] = "$lch: $optVal";
                    } else {
                        throw new InvalidArgumentException("Headers to be signed must be single values, found $c values for header '$h'.");
                    }
                } else {
                    throw new InvalidArgumentException("Headers to be signed must be single values, did not find header '$h'.");
                }
            }
        }

        $signing_string = implode("\n", $signingParts);
        $middlewareLogger->debug("Signing string:\n$signing_string", "signing\\signature");

        // getting the keyId before signing it, because if the auth provider is an AbstractRequestingAuthenticationDetailsProvider,
        // e.g InstancePrincipalsAuthProvider, then getting the key id might trigger a refresh of the session keys.
        // Let's do that now, rather than after signing, so the key id and the private key used for signing are in sync.
        $keyId = $this->auth_provider->getKeyId();

        $signature = AbstractClient::sign_string(
            $signing_string,
            $this->auth_provider->getPrivateKey(),
            $this->auth_provider->getKeyPassphrase()
        );

        $authorization_header = "Signature version=\"1\",keyId=\"$keyId\",algorithm=\"rsa-sha256\",headers=\"$headersString\",signature=\"$signature\"";

        $request = $request->withHeader(Constants::AUTHORIZATION_HEADER_NAME, $authorization_header);

        AbstractClient::logHeaders(new RedactingLogAdapterDecorator($middlewareLogger->scope("requestHeaders")), $request->getHeaders());
        AbstractClient::logHeaders($middlewareLogger->scope("requestHeaders\\sensitive"), $request->getHeaders());
        return $request;
    }

    private static function logHeaders($logger, $headers)
    {
        if ($logger->isDebugEnabled()) {
            $str = "Request headers:";
            foreach ($headers as $name => $values) {
                if (is_array($values)) {
                    foreach ($values as $item) {
                        $str .= PHP_EOL . $name . ': ' . $item;
                    }
                } else {
                    $str .= PHP_EOL . $name . ': ' . $values;
                }
            }
            $logger->debug($str);
        }
    }

    protected static function sign_string($data, $private_key, $passphrase)
    {
        if ($private_key instanceof OpenSSLAsymmetricKey) {
            $parsedKey = $private_key;
        } else {
            $parsedKey = openssl_pkey_get_private($private_key, $passphrase);
            if (!$parsedKey) {
                throw new InvalidArgumentException('Error reading private key (' . StringUtils::get_type_or_class($private_key) . ')');
            }
        }

        openssl_sign($data, $signature, $parsedKey, OPENSSL_ALGO_SHA256);

        return base64_encode($signature);
    }

    public function logger($logName = null) // : LogAdapterInterface
    {
        $logger = Logger::getGlobalLogAdapter();
        if ($this->logAdapter != null) {
            $logger = $this->logAdapter;
        }
        if ($logName == null || strlen($logName) == 0) {
            return new NamedLogAdapterDecorator(static::class, $logger);
        } else {
            return new NamedLogAdapterDecorator(static::class . "\\" . $logName, $logger);
        }
    }

    public function setLogAdapter(LogAdapterInterface $logAdapter)
    {
        $this->globalLogAdapter = $logAdapter;
    }

    public function callApi($httpMethod, $endpoint, $opts)
    {
        return $this->callApiAsync($httpMethod, $endpoint, $opts)->wait();
    }

    public function callApiAsync($httpMethod, $endpoint, $opts)
    {
        $request = $this->initRequest($httpMethod, $endpoint, $opts);
        $responsePromise = $this->client->sendAsync($request)->then(
            function ($__response) use ($opts) {
                if (isset($opts['response_body_type']) && $opts['response_body_type'] == 'binary') {
                    return new OciResponse(
                        $__response->getStatusCode(),
                        $__response->getHeaders(),
                        $__response->getBody(),
                        null
                    );
                }
                return new OciResponse(
                    $__response->getStatusCode(),
                    $__response->getHeaders(),
                    null,
                    json_decode($__response->getBody())
                );
            },
            function ($__e) {
                return HttpUtils::processBadResponseException($__e, false);
            }
        );
        return $responsePromise;
    }

    private function initRequest($method, $endpoint, $opts)
    {
        $headers = isset($opts['headers']) ? $opts['headers'] + self::DEFAULT_HEADERS : self::DEFAULT_HEADERS;
        $body = isset($opts['body']) ? $opts['body'] : null;
        return new Request($method, $endpoint, $headers, $body);
    }
}
