<?php

namespace Oracle\Oci\Common\Auth;

use DateInterval;
use Exception;
use InvalidArgumentException;
use Oracle\Oci\Common\Logging\Logger;
use Oracle\Oci\Common\Logging\NamedLogAdapterDecorator;
use Oracle\Oci\Common\StringUtils;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Caches key id (security token), private key, and key passphrase in a PSR-6 cache.
 *
 * Not thread-safe: This auth provider uses a Guzzle client underneath, and Guzzle clients are not thread-safe.
 * It is recommended to create a new instance of this auth provider per thread.
 *
 * Note that it is your responsibility to use a cache implementation that is secure.
 */
class CachingRequestingAuthProvider implements AuthProviderInterface, RegionProviderInterface, RefreshableOnNotAuthenticatedInterface
{
    /**
     * This is the default time that tokens will be cached. After 20 minutes, the decorated auth provider will be asked for
     * new credentials.
     * You can also invalidate the cache and ask for new credentials by calling the refresh() method.
     */
    const DEFAULT_TOKEN_LIFETIME = "PT20M"; // 20 minutes
    const TOKEN_LIFETIME_PARAM = "tokenLifetime";
    const ALLOWED_PARAMS = [
        self::TOKEN_LIFETIME_PARAM => ['string', DateInterval::class]
    ];
    const REQUIRED_PARAMS = [];

    /**
     * @var NamedLogAdapterDecorator
     */
    protected $logger;

    /**
     * @var NamedLogAdapterDecorator
     */
    private $sensitiveLogger;

    /**
     * @var AbstractRequestingAuthenticationDetailsProvider|RefreshableOnNotAuthenticatedInterface
     */
    protected $inner;

    /**
     * @var CacheItemPoolInterface
     */
    protected $cache;

    /**
     * @var DateInterval|string
     */
    protected $tokenLifetime;

    /**
     * Create a new caching auth provider.
     * @param AbstractRequestingAuthenticationDetailsProvider $inner the decorated auth provider that actually supplies the key id, private key, and passphrase.
     * @param CacheItemPoolInterface $cache the cache implementation
     * @param array $params optional parameters; see ALLOWED_PARAMS for allowed parameters names and their types.
     */
    public function __construct(
        AbstractRequestingAuthenticationDetailsProvider $inner,
        CacheItemPoolInterface $cache,
        $params=[]
    ) {
        $this->logger = Logger::logger(static::class);
        $this->sensitiveLogger = $this->logger->scope("sensitive");
        if (!($inner instanceof RefreshableOnNotAuthenticatedInterface)) {
            throw new InvalidArgumentException("The inner auth provider must be an instance of RefreshableOnNotAuthenticatedInterface, was " . StringUtils::get_type_or_class($inner));
        }
        $this->inner = $inner;
        $this->cache = $cache;
        $this->tokenLifetime = new DateInterval(self::DEFAULT_TOKEN_LIFETIME);

        if (!is_array($params) || array_keys($params) === range(0, count($params) - 1)) {
            throw new InvalidArgumentException("The parameter should be an associative array");
        }

        foreach ($params as $k => $v) {
            if (array_key_exists($k, self::ALLOWED_PARAMS)) {
                $this->{$k} = StringUtils::checkType($k, $v, self::ALLOWED_PARAMS);
            } else {
                throw new InvalidArgumentException("Parameter '$k' invalid");
            }
        }

        if (is_string($this->tokenLifetime)) {
            $this->tokenLifetime = new DateInterval($this->tokenLifetime);
        }
    }

    public function isRefreshableOnNotAuthenticated()
    {
        return $this->inner->isRefreshableOnNotAuthenticated();
    }

    public function getRegion() // : ?Region
    {
        if ($this->inner instanceof RegionProviderInterface) {
            return $this->inner->getRegion();
        } else {
            return null;
        }
    }

    public function getCacheKey()
    {
        $prefix = str_replace('\\', '_', StringUtils::get_type_or_class($this->inner));
        return ['keyId' => $prefix . ".keyId", 'privateKey' => $prefix . ".privateKey", 'keyPassphrase' => $prefix . ".keyPassphrase" ];
    }

    public function invalidateCache()
    {
        $this->logger->debug("Invalidating cache");
        $cacheKey = $this->getCacheKey();
        try {
            $this->cache->deleteItem($cacheKey['keyId']);
        } catch (Exception $e) {
            $this->logger->warn("Failed to delete keyId from cache. Reason: $e");
        }

        try {
            $this->cache->deleteItem($cacheKey['privateKey']);
        } catch (Exception $e) {
            $this->logger->warn("Failed to delete privateKey from cache. Reason: $e");
        }
        
        try {
            $this->cache->deleteItem($cacheKey['keyPassphrase']);
        } catch (Exception $e) {
            $this->logger->warn("Failed to delete keyPassphrase from cache. Reason: $e");
        }
    }

    public function getKeyId() // : string
    {
        $cacheKey = $this->getCacheKey();
        try {
            if ($this->cache->hasItem($cacheKey['keyId'])) {
                $item = $this->cache->getItem($cacheKey['keyId']);
                $keyId = $item->get();
                $this->logger->debug("Using cached keyId");
                $this->sensitiveLogger->debug("Using cached keyId: $keyId");
                return $keyId;
            }
        } catch (Exception $e) {
            // ignore, get new token
            $this->logger->warn("Failed to get keyId from cache, getting new keyId. Reason: $e");
        }
        $keyId = $this->inner->getKeyId();

        try {
            $item = $this->cache->getItem($cacheKey['keyId']);
            $item->set($keyId);
            $item->expiresAfter($this->tokenLifetime);
            $this->cache->save($item);
            $this->sensitiveLogger->debug("Cached keyId: $keyId");
        } catch (Exception $e) {
            $this->logger->warn("Failed to put keyId in cache. Reason: $e");
        }

        return $keyId;
    }

    public function getKeyPassphrase() // : ?string
    {
        $cacheKey = $this->getCacheKey();
        try {
            if ($this->cache->hasItem($cacheKey['keyPassphrase'])) {
                $item = $this->cache->getItem($cacheKey['keyPassphrase']);
                $keyPassphrase = $item->get();
                $this->logger->debug("Using cached keyPassphrase");
                $this->sensitiveLogger->debug("Using cached keyPassphrase: $keyPassphrase");
                return $keyPassphrase;
            }
        } catch (Exception $e) {
            // ignore, get new token
            $this->logger->warn("Failed to get keyPassphrase from cache, getting new keyPassphrase. Reason: $e");
        }

        $keyPassphrase = $this->inner->getKeyPassphrase();

        try {
            $item = $this->cache->getItem($cacheKey['keyPassphrase']);
            $item->set($keyPassphrase);
            $item->expiresAfter($this->tokenLifetime);
            $this->cache->save($item);
            $this->sensitiveLogger->debug("Cached keyPassphrase: $keyPassphrase");
        } catch (Exception $e) {
            $this->logger->warn("Failed to put keyPassphrase in cache. Reason: $e");
        }

        return $keyPassphrase;
    }

    public function getPrivateKey() // : string
    {
        $cacheKey = $this->getCacheKey();
        try {
            if ($this->cache->hasItem($cacheKey['privateKey'])) {
                $item = $this->cache->getItem($cacheKey['privateKey']);
                $privateKey = $item->get();
                $this->logger->debug("Using cached privateKey");
                $this->sensitiveLogger->debug("Using cached privateKey: $privateKey");
                return $privateKey;
            }
        } catch (Exception $e) {
            // ignore, get new token
            $this->logger->warn("Failed to get privateKey from cache, getting new privateKey. Reason: $e");
        }

        $privateKey = $this->inner->getPrivateKey();
        
        try {
            $item = $this->cache->getItem($cacheKey['privateKey']);

            $success = true;
            if (!is_string($privateKey)) {
                $success = openssl_pkey_export($privateKey, $privateKeyStr, $this->getKeyPassphrase());
            } else {
                $privateKeyStr = $privateKey;
            }
            if ($success) {
                $item->set($privateKeyStr);
                $item->expiresAfter($this->tokenLifetime);
                $this->cache->save($item);
                $this->sensitiveLogger->debug("Cached privateKey: $privateKeyStr");
            } else {
                $this->logger->warn("Failed to put privateKey in cache: could not export private key");
            }
        } catch (Exception $e) {
            $this->logger->warn("Failed to put privateKey in cache. Reason: $e");
        }
        return $privateKey;
    }

    /**
     * Gets a security token from the federation endpoint. This will always retreive
     * a new token from the federation endpoint and does not use a cached token.
     * @return string A security token that can be used to authenticate requests.
     */
    public function refresh() // : string
    {
        $this->invalidateCache();
        $token = $this->inner->refresh();

        $this->getKeyId();
        $this->getPrivateKey();
        $this->getKeyPassphrase();

        return $token;
    }
}
