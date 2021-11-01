<?php

namespace Oracle\Oci\Common\Auth;

use GuzzleHttp\Client;
use InvalidArgumentException;
use Oracle\Oci\Common\AbstractClient;
use Oracle\Oci\Common\FederationSigningStrategy;
use Oracle\Oci\Common\Logging\Logger;
use Oracle\Oci\Common\OciException;
use Oracle\Oci\Common\Realm;
use Oracle\Oci\Common\Region;
use Oracle\Oci\Common\StringUtils;
use Oracle\Oci\Common\UserAgent;

interface FederationClientInterface
{
    /**
     * Gets a security token from the federation endpoint. May use a cached token if
     * it judged to still be valid.
     * @return string A security token that can be used to authenticate requests.
     */
    public function getSecurityToken(); // : string

    /**
     * Gets a security token from the federation endpoint. This will always retreive
     * a new token from the federation endpoint and does not use a cached token.
     * @return string A security token that can be used to authenticate requests.
     */
    public function refreshAndGetSecurityToken(); // : string

    /**
     * Get a claim embedded in the security token. May use the cached token if it is
     * judged to still be valid.
     * @return string claim embedded in the security token
     */
    public function getStringClaim(/*string*/ $key); // : string
}

class X509FederationClient extends AbstractClient implements FederationClientInterface
{
    const ALLOWED_PARAMS = [
        "region", "federationEndpoint", "tenancyId", "leafCertificateSupplier", "intermediateCertificateSuppliers", "purpose"];
    
    const DEFAULT_PURPOSE = "DEFAULT";
    const DEFAULT_FINGERPRINT_ALGORITHM = "SHA256";
    const METADATA_SERVICE_BASE_URL = "http://169.254.169.254/opc/v2/";

    /*X509CertificateSupplier*/ private $leafCertificateSupplier;
    /*string*/ private $tenancyId;
    /*X509CertificateSupplier[]*/ private $intermediateCertificateSuppliers;
    /*SessionKeySupplierInterface*/ private $sessionKeySupplier;
    /*string*/ private $purpose;

    /*string*/ private $federationEndpoint;

    /*volatile SecurityTokenAdapter*/ private $securityTokenAdapter = null;
    /*LogAdapterInterface*/ private $logger;

    public function __construct(SessionKeySupplierInterface $sessionKeySupplier, $params=[])
    {
        $this->logger = Logger::logger(static::class);

        $this->sessionKeySupplier = $sessionKeySupplier;
        $this->purpose = X509FederationClient::DEFAULT_PURPOSE;

        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter should be an associative array");
        }

        foreach ($params as $k => $v) {
            if (!in_array($k, X509FederationClient::ALLOWED_PARAMS)) {
                throw new InvalidArgumentException("Parameter '$k' invalid");
            }
            $this->{$k} = $v;
        }

        $this->autoDetectUsingMetadataUrl();

        $this->federationHttpClient = new Client();
        $this->securityTokenAdapter = new SecurityTokenAdapter(null, $this->sessionKeySupplier);

        parent::__construct(
            "https://auth.{region}.oraclecloud.com/v1/x509",
            new X509AuthProvider($this->leafCertificateSupplier, $this->tenancyId),
            FederationSigningStrategy::getSingleton(),
            $this->region,
            $this->federationEndpoint
        );
    }

    /**
     * Gets a security token from the federation endpoint. May use a cached token if
     * it judged to still be valid.
     * @return string A security token that can be used to authenticate requests.
     */
    public function getSecurityToken() // : string
    {
        if ($this->securityTokenAdapter->isValid()) {
            return $this->securityTokenAdapter->getSecurityToken();
        }

        return $this->refreshAndGetSecurityTokenInner(true);
    }

    /**
     * Gets a security token from the federation endpoint. This will always retreive
     * a new token from the federation endpoint and does not use a cached token.
     * @return string A security token that can be used to authenticate requests.
     */
    public function refreshAndGetSecurityToken() // : string
    {
        return $this->refreshAndGetSecurityTokenInner(false);
    }

    /**
     * Gets a security token from the federation endpoint. This will always retreive
     * a new token from the federation endpoint and does not use a cached token.
     * @return string A security token that can be used to authenticate requests.
     */
    protected function refreshAndGetSecurityTokenInner($doFinalTokenValidityCheck) // : string
    {
        if (!$doFinalTokenValidityCheck || !$this->securityTokenAdapter->isValid()) {
            // Check again to see if the JWT is still invalid, unless we want to skip that check)
            $this->logger->info("Refreshing session keys.");

            $this->sessionKeySupplier->refreshKeys();
            $this->logger->debug("Refreshed session keys.");

            // we know this is a X509CertificateSupplierInterface, but with the additional instanceof check,
            // the type checker knows that $this->leafCertificateSupplier->getKeyPair() exists
            if ($this->leafCertificateSupplier instanceof Refreshable && $this->leafCertificateSupplier instanceof X509CertificateSupplierInterface) {
                $this->logger->debug("Refreshing leaf certificate.");
                try {
                    $this->leafCertificateSupplier->refresh();
                } catch (RefreshFailedException $e) {
                    throw new OciException("Failed to refresh leaf certificate", $e->getCode(), $e);
                }
                $this->logger->debug("Refreshed leaf certificate.");

                // When using default purpose (ex, instance principals), the token request should always be signed with the same tenant id as the certificate.
                // For other purposes, the tenant id can be different.
                if ($this->purpose == X509FederationClient::DEFAULT_PURPOSE) {
                    $newTenancyId = X509FederationClient::getTenancyIdFromCertificate(
                        $this->leafCertificateSupplier->getKeyPair()->getPublicKey()
                    );

                    $this->logger->debug("Comparing tenancyId from certificate '$newTenancyId' to configured tenancyId '{$this->tenancyId}'.");

                    if ($this->tenancyId != $newTenancyId) {
                        throw new InvalidArgumentException("The tenancy id should never be changed in cert file!");
                    }
                }
            } else {
                $this->logger->debug("Leaf certificate is not refreshable.");
            }
            $supplierCount = count($this->intermediateCertificateSuppliers);
            $supplierIndex = 0;
            foreach ($this->intermediateCertificateSuppliers as $s) {
                ++$supplierIndex;
                if ($s instanceof Refreshable) {
                    $this->logger->debug("Refreshing intermediate certificate $supplierIndex of {$supplierCount}.");
                    try {
                        $s->refresh();
                    } catch (RefreshFailedException $e) {
                        throw new OciException("Failed to refresh intermediate certificate", $e->getCode(), $e);
                    }
                    $this->logger->debug("Refreshed intermediate certificate $supplierIndex of $supplierCount.");
                } else {
                    $this->logger->debug("Intermediate certificate $supplierIndex of $supplierCount is not refreshable.");
                }
            }

            $this->securityTokenAdapter = new SecurityTokenAdapter($this->getSecurityTokenFromServer(), $this->sessionKeySupplier);
        }
        return $this->securityTokenAdapter->getSecurityToken();
    }

    private function getSecurityTokenFromServer() // : SecurityTokenAdapter
    {
        $this->logger->debug("Getting security token from the auth server.");

        $keyPair = $this->sessionKeySupplier->getKeyPair();
        if ($keyPair == null) {
            throw new InvalidArgumentException("Key pair for session was not provided by session key supplier.");
        }

        $publicKey = $keyPair->getPublicKey();
        if ($publicKey == null) {
            throw new InvalidArgumentException("Public key is not present in session key pair.");
        }

        $certificateAndKeyPair = $this->leafCertificateSupplier->getKeyPair();
        if ($certificateAndKeyPair == null) {
            throw new InvalidArgumentException("Certificate/key pair was not provided by leaf certificate supplier.");
        }

        $leafCertificate = $certificateAndKeyPair->getPublicKey();
        if ($leafCertificate == null) {
            throw new InvalidArgumentException("Leaf certificate is not present in leaf certificate/key pair.");
        }

        if ($certificateAndKeyPair->getPrivateKey() == null) {
            throw new InvalidArgumentException("Leaf certificate's private key is not present in leaf certificate/key pair.");
        }

        $intermediateStrings = null;
        if ($this->intermediateCertificateSuppliers != null && count($this->intermediateCertificateSuppliers) > 0) {
            $this->logger->debug("Intermediate certificate(s) were supplied.");
            $intermediateStrings = [];
            $supplierCount = count($this->intermediateCertificateSuppliers);
            $supplierIndex = 0;
            foreach ($this->intermediateCertificateSuppliers as $s) {
                ++$supplierIndex;
                $kp = $s->getKeyPair();
                if ($kp != null && $kp->getPublicKey() != null) {
                    $keyText = X509FederationClient::base64EncodeIfNecessary($kp->getPublicKey());
                    if ($keyText != null) {
                        $intermediateStrings[] = $keyText;
                    } else {
                        $this->logger->warn("Intermediate certificate $supplierIndex of $supplierCount had an unknown format: "
                            . StringUtils::get_type_or_class($kp->getPublicKey()) . ". Ignoring.");
                    }
                }
            }
        }

        // TODO: retries
        $response = $this->getToken_Helper(
            $publicKey,
            $leafCertificate,
            $this->purpose,
            X509FederationClient::DEFAULT_FINGERPRINT_ALGORITHM,
            $intermediateStrings
        );

        $token = $response->getJson()->token;
        $this->logger->scope("sensitive")->debug("Received federation response, token: '$token'");

        return $token;
    }
    
    protected function getSecurityTokenAdapter()
    {
        return $this->securityTokenAdapter;
    }

    private function getToken_Helper(
        $publicKey,
        $certificate,
        $purpose,
        $fingerprintAlgorithm,
        $intermediateStrings = null
    ) {
        $__headers = ['Content-Type' => 'application/json', 'User-Agent' => UserAgent::getUserAgent()];
        // Create the request body to be sent to the auth service
        $federationRequest = [
            "publicKey" => X509FederationClient::base64EncodeIfNecessary($publicKey),
            "certificate" => X509FederationClient::base64EncodeIfNecessary($certificate),
            "purpose" => $purpose,
            "fingerprintAlgorithm" => $fingerprintAlgorithm
        ];
        if ($intermediateStrings != null) {
            $federationRequest["intermediateCertificates"] = $intermediateStrings;
        }

        $body = json_encode($federationRequest, JSON_UNESCAPED_SLASHES);
        Logger::logger(static::class . "\\sensitive")->debug("Making federation request: " . $body);

        return $this->callApi("POST", "{$this->endpoint}", [ 'headers' => $__headers, 'body' => $body  ]);
    }

    /**
     * Get a claim embedded in the security token. May use the cached token if it is
     * judged to still be valid.
     * @return string claim embedded in the security token
     */
    public function getStringClaim(/*string*/ $key) // : string
    {
        $this->refreshAndGetSecurityTokenInner(true);
        return $this->securityTokenAdapter->getStringClaim($key);
    }


    /**
     * Auto-detect endpoint and certificate information using Instance metadata.
     */
    protected function autoDetectUsingMetadataUrl()
    {
        $this->autoDetectEndpointUsingMetadataUrl();
        $this->autoDetectCertificatesUsingMetadataUrl();
    }

    /**
     * Auto detects the endpoint that should be used when talking to OCI Auth, if no endpoint
     * has been configured already.
     * @return string The auto-detected, or currently set, auth endpoint.
     */
    protected function autoDetectEndpointUsingMetadataUrl() // : string
    {
        if ($this->federationEndpoint == null && $this->region == null) {
            $client = new Client();
            $response = $client->get(
                X509FederationClient::METADATA_SERVICE_BASE_URL . "instance/region",
                [ 'headers' => [ "Authorization" => "Bearer Oracle"] ]
            );
            
            $regionStr = $response->getBody();
            $this->logger->debug("Looking up region for {$regionStr}.");

            // Region.fromRegionId, and fall back to 'region' only for backwards compat.
            $region = Region::getRegion($regionStr);
            if ($region == null) {
                $this->logger->debug(
                    "Region not supported by this version of the SDK, registering region '{$regionStr}' under " . Realm::getRealmForUnknownRegion() . "."
                );
                // Proceed by assuming the region id belongs to the "unknown regions" realm.
                $region = new Region($regionStr, $regionStr, Realm::getRealmForUnknownRegion());
            }
            $this->logger->debug("Using region {$region}.");

            $this->federationEndpoint = "https://auth." . $region->getRegionId() . ".oraclecloud.com/v1/x509";

            $this->logger->debug("Using federation endpoint {$this->federationEndpoint}");
        }
        return $this->federationEndpoint;
    }

    /**
     * Auto detects and configures the certificates needed using Instance metadata.
     *
     */
    protected function autoDetectCertificatesUsingMetadataUrl()
    {
        if ($this->leafCertificateSupplier == null) {
            $this->leafCertificateSupplier =
                    new UrlBasedX509CertificateSupplier(
                        X509FederationClient::METADATA_SERVICE_BASE_URL . "identity/cert.pem",
                        X509FederationClient::METADATA_SERVICE_BASE_URL . "identity/key.pem",
                        null
                    );
            $this->logger->debug("Auto-detected leaf certificate supplier.");
        }

        if ($this->tenancyId == null) {
            $this->tenancyId =
                    X509FederationClient::getTenancyIdFromCertificate(
                        $this->leafCertificateSupplier->getKeyPair()->getPublicKey()
                    );
            $this->logger->debug("Auto-detected tenancyId '{$this->tenancyId}'.");
        }

        if ($this->intermediateCertificateSuppliers == null) {
            $this->intermediateCertificateSuppliers = [];

            $this->intermediateCertificateSuppliers[] = new UrlBasedX509CertificateSupplier(
                X509FederationClient::METADATA_SERVICE_BASE_URL . "identity/intermediate.pem",
                null,
                null
            );
            $this->logger->debug("Auto-detected intermediate certificate supplier.");
        }
    }

    public static function getTenancyIdFromCertificate($certificate) // : string
    {
        $info = openssl_x509_parse($certificate);
        if ($info == false) {
            throw new InvalidArgumentException("Could not get tenancyId from certificate.");
        }

        if (!array_key_exists("name", $info)) {
            throw new InvalidArgumentException("Could not get tenancyId from certificate.");
        }
        $name = $info['name'];
        $parts = explode("/", $name);

        $prefix = "OU=opc-tenant:"; // instance principals
        foreach ($parts as $p) {
            if (substr($p, 0, strlen($prefix)) == $prefix) {
                return substr($p, strlen($prefix));
            }
        }
        $prefix = "O=opc-identity:"; // service principals
        foreach ($parts as $p) {
            if (substr($p, 0, strlen($prefix)) == $prefix) {
                return substr($p, strlen($prefix));
            }
        }

        throw new InvalidArgumentException("Could not get tenancyId from certificate.");
    }

    public static function base64EncodeIfNecessary($data)
    {
        if (is_string($data)) {
            if (strpos("-----BEGIN", $data) == 0) {
                // remove head
                $eolPos = strpos($data, PHP_EOL);
                if ($eolPos == false) {
                    $data = str_replace("\r", "\n", $data);
                    $nPos = strpos($data, "\n");
                    if ($nPos != false) {
                        $nextLinePos = $nPos + 1;
                    } else {
                        $nextLinePos = false;
                    }
                } else {
                    $nextLinePos = $eolPos + strlen(PHP_EOL);
                }
                if ($nextLinePos != false) {
                    $data = substr($data, $nextLinePos);
                }
            }
            $tailPos = strpos($data, "-----END");
            if ($tailPos != false) {
                // remove tail
                $data = substr($data, 0, $tailPos);
            }
            $data = str_replace([PHP_EOL, "\n", "\r"], ["", "", ""], $data);
            return trim($data);
        // } else if ($kp->getPublicKey() instanceof OpenSSLCertificate) {
        //      // TODO: AuthUtils.base64EncodeNoChunking?
        // } else if ($kp->getPublicKey() instanceof OpenSSLAsymmetricKey) {
        //      // TODO: AuthUtils.base64EncodeNoChunking?
        } else {
            return null;
        }
    }
}

class X509AuthProvider implements AuthProviderInterface
{
    /*X509CertificateSupplierInterface*/ private $leafCertificateSupplier;
    /*string*/ private $tenancyId;

    public function __construct(X509CertificateSupplierInterface $leafCertificateSupplier, /*string*/ $tenancyId)
    {
        $this->leafCertificateSupplier = $leafCertificateSupplier;
        $this->tenancyId = $tenancyId;
    }

    public function getPrivateKey() //  : string
    {
        return $this->leafCertificateSupplier->getKeyPair()->getPrivateKey();
    }

    public function getKeyPassphrase() // : ?string
    {
        return null;
    }

    public function getKeyId() // : string
    {
        $fingerprint = strtoupper(
            openssl_x509_fingerprint(
                $this->leafCertificateSupplier->getKeyPair()->getPublicKey(),
                X509FederationClient::DEFAULT_FINGERPRINT_ALGORITHM
            )
        );
        
        $parts = str_split($fingerprint, 2);
        $fingerprintWithColons = implode(":", $parts);

        $keyId = "{$this->tenancyId}/fed-x509-sha256/$fingerprintWithColons";
        Logger::logger(static::class . "\\sensitive")->debug("Using keyId '$keyId'.");
        return $keyId;
    }
}
