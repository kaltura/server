<?php

namespace Oracle\Oci\Common;

use Psr\Http\Message\RequestInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use InvalidArgumentException;
use Oracle\Oci\Common\Logging\LogAdapterInterface;
use Oracle\Oci\Common\Logging\NoOpLogAdapter;

abstract class AbstractClient
{
    /*LogAdapterInterface*/ protected static $globalLogAdapter;
    /*LogAdapterInterface*/ protected $logAdapter;

    /*AuthProviderInterface*/ protected $auth_provider;
    /*?Region*/ protected $region;

    /*string*/ protected $endpoint;
    protected $client;

    public function __construct(
        $endpointTemplate,
        AuthProviderInterface $auth_provider,
        $region=null,
        $endpoint=null
    ) {
        $this->auth_provider = $auth_provider;

        if ($auth_provider instanceof RegionProvider) {
            $this->region = $auth_provider->getRegion();
        }
        if ($region != null) {
            if ($region instanceof Region) {
                $this->region = $region;
            } else {
                $knownRegion = Region::getRegion($region);
                if ($knownRegion == null) {
                    // forward-compatibility for unknown regions
                    $realm = Realm::getRealmForUnknownRegion();
                    $endpoint = str_replace('{region}', $region, $endpointTemplate);
                    $endpoint = str_replace('{secondLevelDomain}', $realm->getRealmDomainComponent(), $endpoint);
                    $this->region = null;
                    $this->getLogAdapter()->log(
                        "Region $region is unknown, assuming it to be in realm $realm. Setting endpoint to $endpoint",
                        LOG_INFO,
                        [],
                        static::class
                    );
                } else {
                    $this->region = $knownRegion;
                }
            }
        }
        if ($this->region == null && $endpoint == null) {
            throw new InvalidArgumentException('Neither region nor endpoint is set.');
        }

        if ($endpoint != null) {
            $this->endpoint = $endpoint;
        } else {
            $this->endpoint = str_replace('{region}', $this->region->getRegionId(), $endpointTemplate);
            $this->endpoint = str_replace('{secondLevelDomain}', $this->region->getRealm()->getRealmDomainComponent(), $this->endpoint);
        }
        $this->getLogAdapter()->log(
            "Final endpoint: {$this->endpoint}",
            LOG_DEBUG,
            [],
            static::class
        );

        $handler = new CurlHandler();
        $stack = HandlerStack::create($handler);

        // place signing middleware after prepare-body so it can access Content-Length header
        $stack->after('prepare_body', Middleware::mapRequest(function (RequestInterface $request) {
            $this->getLogAdapter()->log(
                "Request URI: " . $request->getUri(),
                LOG_DEBUG,
                [],
                static::class . "\\middleware\\uri"
            );

            // headers required for all HTTP verbs
            $headers = "date (request-target) host";

            // example: Thu, 05 Jan 2014 21:31:40 GMT
            $date=gmdate("D, d M Y H:i:s T", time());
            $method = strtolower($request->getMethod());
            $request_target = $request->getRequestTarget();
            $host = $request->getHeader('Host')[0];

            $request = $request->withHeader('Date', $date);

            $signing_string = "date: $date\n(request-target): $method $request_target\nhost: $host";

            // additional required headers for POST and PUT requests
            if ($method == 'post' || $method == 'put') {
                $clHeaders = $request->getHeader('Content-Length');
                if ($clHeaders != null && count($clHeaders) > 0) {
                    $content_length = $clHeaders[0];
                } else {
                    // if content length is 0 we still need to explicitly send the Content-Length header
                    $content_length = 0;
                    $request = $request->withHeader('Content-Length', 0);
                }

                $content_type = $request->getHeader('Content-Type')[0];
                $content_sha256 = base64_encode(hex2bin(hash("sha256", $request->getBody())));

                $request = $request->withHeader('x-content-sha256', $content_sha256);

                $headers = $headers . " content-length content-type x-content-sha256";
                $signing_string = $signing_string . "\ncontent-length: $content_length\ncontent-type: $content_type\nx-content-sha256: $content_sha256";
            }

            $this->getLogAdapter()->log(
                "Signing string:\n$signing_string",
                LOG_DEBUG,
                [],
                static::class . "\\middleware\\signature"
            );

            $signature = $this->sign_string($signing_string, $this->auth_provider->getKeyFilename(), $this->auth_provider->getKeyPassphrase());

            $authorization_header = "Signature version=\"1\",keyId=\"{$this->auth_provider->getKeyId()}\",algorithm=\"rsa-sha256\",headers=\"$headers\",signature=\"$signature\"";
            $request = $request->withHeader('Authorization', $authorization_header);

            if ($this->getLogAdapter()->isLogEnabled(LOG_DEBUG, static::class . "\\middleware\\requestHeaders")) {
                $str = "Request headers:";
                foreach ($request->getHeaders() as $name => $values) {
                    if (is_array($values)) {
                        foreach ($values as $item) {
                            $str .= PHP_EOL . $name . ': ' . $item;
                        }
                    } else {
                        $str .= PHP_EOL . $name . ': ' . $values;
                    }
                }
                $this->getLogAdapter()->log(
                    $str,
                    LOG_DEBUG,
                    [],
                    static::class . "\\middleware\\requestHeaders"
                );
            }

            return $request;
        }));

        $this->client = new Client([
            'handler' => $stack
        ]);
    }

    protected function sign_string($data, $key_path, $passphrase)
    {
        $pkeyid = openssl_pkey_get_private($key_path, $passphrase);
        if (!$pkeyid) {
            exit('Error reading private key');
        }

        openssl_sign($data, $signature, $pkeyid, OPENSSL_ALGO_SHA256);

        return base64_encode($signature);
    }

    public static function getGlobalLogAdapter() // : LogAdapterInterface
    {
        if (AbstractClient::$globalLogAdapter == null) {
            AbstractClient::setGlobalLogAdapter(new NoOpLogAdapter());
        }
        return AbstractClient::$globalLogAdapter;
    }

    public static function setGlobalLogAdapter(LogAdapterInterface $logAdapter)
    {
        AbstractClient::$globalLogAdapter = $logAdapter;
    }

    public function getLogAdapter() // : LogAdapterInterface
    {
        if ($this->logAdapter != null) {
            return $this->logAdapter;
        }
        return AbstractClient::getGlobalLogAdapter();
    }

    public function setLogAdapter(LogAdapterInterface $logAdapter)
    {
        $this->globalLogAdapter = $logAdapter;
    }
}
