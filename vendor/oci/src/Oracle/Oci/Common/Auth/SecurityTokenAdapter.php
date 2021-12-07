<?php

namespace Oracle\Oci\Common\Auth;

use Oracle\Oci\Common\Logging\Logger;
use Oracle\Oci\Common\StringUtils;

class SecurityTokenAdapter
{
    /*string*/ private $securityToken;
    /*JWT*/ private $jwt;
    /*SessionKeySupplierInterface*/ private $sessionKeySupplier;
    /*LogAdapterInterface*/ private $logger;

    public function __construct(
        /*string*/
        $securityToken,
        SessionKeySupplierInterface $sessionKeySupplier
    ) {
        $this->logger = Logger::logger(static::class);

        $this->sessionKeySupplier = $sessionKeySupplier;
        $this->securityToken = $securityToken;
        if ($securityToken != null) {
            $this->jwt = $this->parse($securityToken);
            $this->logger->scope("jwt\\sensitive")->debug("Parsed JWT, claims: " . json_encode($this->jwt->getClaims()));
        } else {
            $this->jwt = null;
        }
    }

    private function parse(/*string*/ $token)
    {
        return new JWT($token);
    }

    public function isValid() // : boolean
    {
        if ($this->jwt == null) {
            $this->logger->debug("Security token is not valid.");
            return false;
        }
        $jwtExp = $this->jwt->getExpirationTime();
        $now = time();
        if ($jwtExp > $now) {
            $verboseLog = $this->logger->scope("verbose");
            $verboseLog->debug("Security token is not expired: '$jwtExp' > '$now'.");
            $this->sessionKeySupplier->getKeyPair()->getPublicKey();
            // Next compare the public key inside the JWT is the same from the supplier.
            // We check this in case secrets service deploys a new key and the JWT is still not expired.
            // In such case, we would want to re-issue the token.
            if (($eFromToken = $this->getStringClaim("e")) != null && ($nFromToken = $this->getStringClaim("n")) != null) {
                $publicKey = openssl_pkey_get_public($this->sessionKeySupplier->getKeyPair()->getPublicKey());
                $details = openssl_pkey_get_details($publicKey);
        
                $nFromKey = StringUtils::base64_to_base64url(base64_encode($details['rsa']['n']));
                $eFromKey = StringUtils::base64_to_base64url(base64_encode($details['rsa']['e']));
        
                // the $nFromKey and $eFromKey might be padded with '=' at the end to be a multiple of 4 characters long
                $nComp = ($nFromKey == ($nFromToken . substr("===", 0, strlen($nFromToken) % 4)));
                $eComp = ($eFromKey == ($eFromToken . substr("===", 0, strlen($eFromToken) % 4)));
                $verboseLog->debug("Comparing token key to supplier key. n: " . ($nComp ? "identical" : "differs") . "; e: " . ($eComp ? "identical" : "differs") . ".");
                return $nComp && $eComp;
            } else {
                $this->logger->warn("JWT in security token did not contain 'e' and 'n' claims, security token is not valid.");
            }
        } else {
            $this->logger->debug("Security token is expired: '$jwtExp' > '$now'.");
        }

        return false;
    }

    public function getStringClaim(/*string*/ $key) // : string
    {
        if ($this->jwt == null) {
            $this->logger->debug("Security token is not valid (null).");
            return null;
        }
        return $this->jwt->getClaims()->{$key};
    }

    public function getSecurityToken() // : string
    {
        return $this->securityToken;
    }

    public function getJwt() // : JWT
    {
        return $this->jwt;
    }
}

class JWT
{
    private $header;
    private $payload;
    private $signature;
    private $claims;


    public function __construct(/*string*/ $token)
    {
        $parts = explode(".", $token);
        $this->header = json_decode(StringUtils::base64url_decode($parts[0]));
        Logger::logger(static::class . "\\sensitive")->debug("JWT Header: " . StringUtils::base64url_decode($parts[0]));
        $this->payload = json_decode(StringUtils::base64url_decode($parts[1]));
        Logger::logger(static::class . "\\sensitive")->debug("JWT Payload: " . StringUtils::base64url_decode($parts[1]));
        $this->signature = StringUtils::base64url_decode($parts[2]);
        $this->claims = json_decode($this->payload->jwk);
    }

    public function getHeader()
    {
        return $this->header;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    public function getSignature()
    {
        return $this->signature;
    }

    public function getClaims()
    {
        return $this->claims;
    }

    public function getExpirationTime()
    {
        return $this->getPayload()->exp;
    }
}
