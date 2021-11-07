<?php

namespace Oracle\Oci\Common\Auth;

use OpenSSLAsymmetricKey;
use Oracle\Oci\Common\ConfigFile;
use Oracle\Oci\Common\Logging\Logger;
use Oracle\Oci\Common\Realm;
use Oracle\Oci\Common\Region;

/**
 * Simplest interface required to authenticate to OCI services.
 */
interface AuthProviderInterface
{
    /**
     * Return the private key.
     *
     * @return OpenSSLAsymmetricKey|string either an already parsed OpenSSLAsymmetricKey, a filename in the format scheme://path/to/file.pem, or a PEM formatted private key as a string.
     */
    public function getPrivateKey(); //  : string;

    /**
     * Return the key passphrase, or null if none.
     *
     * @return string|null key passphrase, or null if none
     */
    public function getKeyPassphrase(); // : ?string;

    /**
     * Return the key id.
     *
     * @return string key id
     */
    public function getKeyId(); // : string;
}

/**
 * Additional interface for auth providers that can also provide the region to use.
 */
interface RegionProviderInterface
{
    /**
     * Get the region.
     *
     * @return Region|null the region to use, or null of none set.
     */
    public function getRegion(); // : ?Region;
}

/**
 * Additional interface for auth providers that can also refresh their credentials when receiving an authentication error.
 */
interface RefreshableOnNotAuthenticatedInterface
{
    /**
     * Return true if this auth provider can refresh its credentials on an authentication error.
     * @return true if the credentials can be refreshed on an authentication error
     */
    public function isRefreshableOnNotAuthenticated(); // : bool

    /**
     * Refresh the credentials.
     */
    public function refresh();
}

/**
 * User auth provider.
 */
class UserAuthProvider implements AuthProviderInterface
{
    /*string*/ protected $tenancy_id;
    /*string*/ protected $user_id;
    /*string*/ protected $fingerprint;

    /**
     * Either an already parsed OpenSSLAsymmetricKey, a filename in the format scheme://path/to/file.pem, or a PEM formatted private key as a string.
     */
    /*string*/ protected $private_key;
    /*?string*/ protected $key_passphrase;

    public function __construct(
        /*string*/
        $tenancy_id,
        /*string*/
        $user_id,
        /*string*/
        $fingerprint,
        /*string*/
        $private_key,
        /*string*/
        $key_passphrase = null
    ) {
        $this->tenancy_id = $tenancy_id;
        $this->user_id = $user_id;
        $this->fingerprint = $fingerprint;
        $this->private_key = $private_key;
        $this->key_passphrase = $key_passphrase;
    }

    public function getTenancyId() // : string
    {
        return $this->tenancy_id;
    }
    public function getUserId() // : string
    {
        return $this->user_id;
    }
    public function getFingerprint() // : string
    {
        return $this->fingerprint;
    }

    public function getPrivateKey() // : string
    {
        return $this->private_key;
    }

    public function getKeyPassphrase() // : ?string
    {
        return $this->key_passphrase;
    }

    public function getKeyId() // : string
    {
        return "{$this->tenancy_id}/{$this->user_id}/{$this->fingerprint}";
    }
}

class ConfigFileAuthProvider implements AuthProviderInterface, RegionProviderInterface
{
    /*ConfigFile*/ protected $cf;
    /*string*/ protected $tenancy_id;
    /*string*/ protected $user_id;
    /*string*/ protected $fingerprint;
    /*string*/ protected $private_key;
    /*?string*/ protected $key_passphrase;
    /*?Region*/ protected $region;

    public function __construct(
        ConfigFile $cf = null
    ) {
        if ($cf != null) {
            $this->cf = $cf;
        } else {
            $this->cf = ConfigFile::loadDefault();
        }
        $this->tenancy_id = $this->cf->get("tenancy");
        $this->user_id = $this->cf->get("user");
        $this->fingerprint = $this->cf->get("fingerprint");

        $filename = str_replace('/', DIRECTORY_SEPARATOR, $this->cf->get("key_file"));
        if (strlen($filename) >= 2 && substr($filename, 0, 2) == "~" . DIRECTORY_SEPARATOR) {
            $this->private_key = "file://" . ConfigFile::getUserHome() . DIRECTORY_SEPARATOR . substr($filename, 2);
        } else {
            $this->private_key = "file://" . $filename;
        }

        $this->key_passphrase = $this->cf->get("pass_phrase");

        $regionStr = $this->cf->get("region");
        if ($regionStr == null || strlen($regionStr) == 0) {
            $this->region = null;
        } else {
            $knownRegion = Region::getRegion($regionStr);
            if ($knownRegion == null) {
                // forward-compatibility for unknown regions
                $realm = Realm::getRealmForUnknownRegion();
                Logger::logger(static::class)->info("Region $regionStr is unknown, assuming it to be in realm $realm.");
                $knownRegion = new Region($regionStr, $regionStr, $realm);
            }
            $this->region = $knownRegion;
        }
    }

    public function getTenancyId() // : string
    {
        return $this->tenancy_id;
    }
    public function getUserId() // : string
    {
        return $this->user_id;
    }
    public function getFingerprint() // : string
    {
        return $this->fingerprint;
    }

    public function getPrivateKey() // : string
    {
        return $this->private_key;
    }

    public function getKeyPassphrase() // : ?string
    {
        return $this->key_passphrase;
    }

    public function getKeyId() // : string
    {
        return "{$this->tenancy_id}/{$this->user_id}/{$this->fingerprint}";
    }

    public function getRegion() // : ?Region
    {
        return $this->region;
    }
}
