<?php

namespace Oracle\Oci\Common\Auth;

use GuzzleHttp\Client;
use Oracle\Oci\Common\Constants;
use Oracle\Oci\Common\Logging\Logger;
use Oracle\Oci\Common\OciException;
use Throwable;

interface X509CertificateSupplierInterface
{
    /**
     * Returns the X509 certificate and private key.  The X509 certificate will always
     * be valid.  The private key may be null for intermediate certificates.  For leaf
     * certificates, the private key will always be valid.
     * @return KeyPair certificate and private key pair.
     */
    public function getKeyPair();
}

interface Refreshable
{
    /**
     * Determine if this object is current.
     * @return bool true if this {@code Object} is currently current, false otherwise.
     */
    public function isCurrent(); // : bool

    /**
     * Update or extend the validity period for this object.
     * @throws RefreshFailedException if the refresh attempt failed.
     */
    public function refresh();
}

class RefreshFailedException extends OciException
{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Throwable $previous = null)
    {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    // custom string representation of object
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

class UrlBasedX509CertificateSupplier implements X509CertificateSupplierInterface, Refreshable
{
    const BEARER_ORACLE = "Bearer Oracle";

    /*string*/ private $publicKeyEndpoint;
    /*string*/ private $privateKeyEndpoint;
    /*KeyPair*/ private $keyPair;
    /*LogAdapterInterface*/ private $logger;
    
    public function __construct($publicKeyEndpoint, $privateKeyEndpoint)
    {
        $this->logger = Logger::logger(static::class);

        $this->publicKeyEndpoint = $publicKeyEndpoint;
        $this->privateKeyEndpoint = $privateKeyEndpoint;
        $this->refresh();
    }

    /**
     * Returns the X509 certificate and private key.  The X509 certificate will always
     * be valid.  The private key may be null for intermediate certificates.  For leaf
     * certificates, the private key will always be valid.
     * @return KeyPair certificate and private key pair.
     */
    public function getKeyPair()
    {
        return $this->keyPair;
    }

    public function refresh()
    {
        $client = new Client();

        $privateKey = null;
        if ($this->privateKeyEndpoint != null) {
            $response = $client->get(
                $this->privateKeyEndpoint,
                [ 'headers' => [ Constants::AUTHORIZATION_HEADER_NAME => UrlBasedX509CertificateSupplier::BEARER_ORACLE] ]
            );
            
            $privateKey = $response->getBody()->getContents();
            $this->logger->debug("Successfully looked up private key from IMDS.");
        }

        $response = $client->get(
            $this->publicKeyEndpoint,
            [ 'headers' => [ Constants::AUTHORIZATION_HEADER_NAME => UrlBasedX509CertificateSupplier::BEARER_ORACLE] ]
        );
        
        $publicKey = $response->getBody()->getContents();
        $this->logger->debug("Successfully looked up public key from IMDS.");

        $this->keyPair = new KeyPair($publicKey, $privateKey);
        return $this->keyPair;
    }

    /**
     * So far we don't care whether the certificate is current or not.
     * @return boolean false always.
     */
    public function isCurrent()
    {
        return false;
    }
}
