<?php

namespace Oracle\Oci\Common\Auth;

use GuzzleHttp\Client;
use InvalidArgumentException;
use OpenSSLAsymmetricKey;
use Oracle\Oci\Common\Logging\LogAdapterInterface;
use Oracle\Oci\Common\Logging\Logger;
use Oracle\Oci\Common\Realm;
use Oracle\Oci\Common\Region;
use Oracle\Oci\Common\StringUtils;

class KeyPair
{
    private $publicKey;

    /**
     * Either an already parsed OpenSSLAsymmetricKey, a filename in the format scheme://path/to/file.pem, or a PEM formatted private key as a string.
     */
    private $privateKey;
    
    public function __construct(
        $publicKey,
        $privateKey
    ) {
        if (is_string($publicKey) && strpos($publicKey, "PRIVATE KEY") !== false) {
            throw new InvalidArgumentException("Private key provided as public key.");
        }
        if (is_string($privateKey) && strpos($privateKey, "PUBLIC KEY") !== false) {
            throw new InvalidArgumentException("Public key provided as private key.");
        }
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
    }

    /**
     * Return the private key.
     *
     * @return OpenSSLAsymmetricKey|string either an already parsed OpenSSLAsymmetricKey, a filename in the format scheme://path/to/file.pem, or a PEM formatted private key as a string.
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    public function getPublicKey()
    {
        return $this->publicKey;
    }
}

interface SessionKeySupplierInterface
{
    public function getKeyPair(); // : KeyPair
    public function refreshKeys();
    public function getKeyPassphrase(); // : ?string;
}

/**
 * This is a helper class to generate in-memory temporary session keys.
 */
class SessionKeySupplierImpl implements SessionKeySupplierInterface
{
    /*KeyPair*/ private $keyPair = null;

    public function __construct()
    {
        $this->keyPair = $this->generateKeyPair();
    }

    public function getKeyPair() // : KeyPair
    {
        return $this->keyPair;
    }

    public function refreshKeys()
    {
        $this->keyPair = $this->generateKeyPair();
    }

    public function getKeyPassphrase() // : ?string
    {
        return null;
    }

    protected function generateKeyPair()
    {
        $config = array(
            // TODO: what is the "digest_alg" setting? I can't find that in the OCI Java SDK
            "digest_alg" => "sha512",
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );
           
        // Create the private and public key
        $res = openssl_pkey_new($config);

        // Extract the private key from $res to $privKey
        openssl_pkey_export($res, $privKey);

        // Extract the public key from $res to $pubKey
        $details = openssl_pkey_get_details($res);
        $pubKey = $details["key"];

        return new KeyPair($pubKey, $privKey);
    }
}

class CachingSessionKeySupplier implements SessionKeySupplierInterface
{
    /*SessionKeySupplierInterface*/ private $inner;
    /*KeyPair*/ private $cachedKeyPair = null;

    public function __construct(
        SessionKeySupplierInterface $inner
    ) {
        $this->inner = $inner;
    }

    public function getKeyPair()
    {
        if ($this->cachedKeyPair == null) {
            $this->cacheKeyPair();
        }
        return $this->cachedKeyPair;
    }

    public function refreshKeys()
    {
        $this->inner->refreshKeys();
        $this->cacheKeyPair();
    }

    public function getKeyPassphrase()
    {
        $this->inner->getKeyPassphrase();
    }

    protected function cacheKeyPair()
    {
        if ($this->inner->getKeyPair()->getPrivateKey() instanceof OpenSSLAsymmetricKey) {
            $parsedKey = $this->inner->getKeyPair()->getPrivateKey();
        } else {
            $parsedKey = openssl_pkey_get_private($this->inner->getKeyPair()->getPrivateKey(), $this->inner->getKeyPassphrase());
            if (!$parsedKey) {
                throw new InvalidArgumentException('Error reading private key');
            }
        }
        $this->cachedKeyPair = new KeyPair($this->inner->getKeyPair()->getPublicKey(), $parsedKey);
    }
}

abstract class AbstractRequestingAuthenticationDetailsProvider implements AuthProviderInterface
{
    const REGION = "region";
    const FEDERATION_CLIENT = "federationClient";
    const SESSION_KEY_SUPPLIER = "sessionKeySupplier";
    const ALLOWED_PARAMS = [
        AbstractRequestingAuthenticationDetailsProvider::REGION => [Region::class, "string"],
        AbstractRequestingAuthenticationDetailsProvider::FEDERATION_CLIENT => FederationClientInterface::class,
        AbstractRequestingAuthenticationDetailsProvider::SESSION_KEY_SUPPLIER => SessionKeySupplierInterface::class
    ];
    const REQUIRED_PARAMS = [];

    /*LogAdapterInterface*/ protected $logger;
    /*FederationClientInterface*/ protected $federationClient;
    /*SessionKeySupplierInterface*/ protected $sessionKeySupplier;
    /*Region*/ protected $region;

    public function __construct($params = [])
    {
        $this->logger = Logger::logger(static::class);

        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter should be an associative array");
        }

        StringUtils::checkAllRequired($params, AbstractRequestingAuthenticationDetailsProvider::REQUIRED_PARAMS);

        foreach ($params as $k => $v) {
            $this->{$k} = StringUtils::checkType($k, $v, AbstractRequestingAuthenticationDetailsProvider::ALLOWED_PARAMS);
        }

        if ($this->sessionKeySupplier == null) {
            $this->sessionKeySupplier = new SessionKeySupplierImpl();
        }
        if ($this->federationClient == null) {
            $this->federationClient = new X509FederationClient($this->sessionKeySupplier);
        }

        $this->sessionKeySupplier = new CachingSessionKeySupplier($this->sessionKeySupplier);

        $this->region = $this->autoDetectRegionUsingMetadataUrl();
    }

    protected function getFederationClient()
    {
        return $this->federationClient;
    }

    protected function getSessionKeySupplier()
    {
        return $this->sessionKeySupplier;
    }

    public function getKeyId() // : string
    {
        return "ST$" . $this->getFederationClient()->getSecurityToken();
    }

    public function getKeyPassphrase() // : ?string
    {
        // no passphrase
        return null;
    }

    public function getPrivateKey() // : string
    {
        return $this->sessionKeySupplier->getKeyPair()->getPrivateKey();
    }
        
    /**
     * Auto detects the region that the instance runs in, if no region
     * has been configured already.
     * @return Region The auto-detected, or currently set, Region.
     */
    protected function autoDetectRegionUsingMetadataUrl() // : Region
    {
        if ($this->region == null) {
            $client = new Client();
            $response = $client->get(
                X509FederationClient::METADATA_SERVICE_BASE_URL . "instance/region",
                [ 'headers' => [ "Authorization" => "Bearer Oracle"] ]
            );
            
            $regionStr = $response->getBody();
            $this->logger->debug("Looking up region for {$regionStr}.");

            // Region.fromRegionId, and fall back to 'region' only for backwards compat.
            $this->region = Region::getRegion($regionStr);
            if ($this->region == null) {
                $this->logger->debug(
                    "Region not supported by this version of the SDK, registering region '{$this->regionStr}' under " . Realm::getRealmForUnknownRegion() . "."
                );
                // Proceed by assuming the region id belongs to the "unknown regions" realm.
                $this->region = new Region($regionStr, $regionStr, Realm::getRealmForUnknownRegion());
            }
            $this->logger->debug("Using region {$this->region}.");
        }
        return $this->region;
    }
}

class InstancePrincipalsAuthProvider extends AbstractRequestingAuthenticationDetailsProvider implements AuthProviderInterface, RegionProviderInterface, RefreshableOnNotAuthenticatedInterface
{
    const ALLOWED_PARAMS = [];
    const REQUIRED_PARAMS = [];

    public function __construct(
        $params=[]
    ) {
        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter should be an associative array");
        }

        $parentParams = [];
        foreach ($params as $k => $v) {
            if (array_key_exists($k, InstancePrincipalsAuthProvider::ALLOWED_PARAMS)) {
                $this->{$k} = StringUtils::checkType($k, $v, InstancePrincipalsAuthProvider::ALLOWED_PARAMS);
            } elseif (array_key_exists($k, AbstractRequestingAuthenticationDetailsProvider::ALLOWED_PARAMS)) {
                $parentParams[$k] = $v;
            } else {
                throw new InvalidArgumentException("Parameter '$k' invalid");
            }
        }

        parent::__construct($parentParams);
    }

    public function getRegion() // : ?Region
    {
        return $this->region;
    }

    /**
     * Gets a security token from the federation endpoint. This will always retreive
     * a new token from the federation endpoint and does not use a cached token.
     * @return string A security token that can be used to authenticate requests.
     */
    public function refresh() // : string
    {
        return $this->getFederationClient()->refreshAndGetSecurityToken();
    }
}
