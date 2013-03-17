<?php
/**
 * @package Core
 * @subpackage utils
 */
class kLock
{
	const LOCK_KEY_PREFIX = '__LOCK';
	const LOCK_GRAB_TRY_INTERVAL = 20000;
	
	/**
	 * @var kBaseCacheWrapper
	 */
	protected $store;
	
	/**
	 * @var string
	 */
	protected $key;

	/**
	 * @param kBaseCacheWrapper $store
	 * @param string $key
	 */
	protected function __construct($store, $key)
	{
		$this->store = $store;
		$this->key = self::LOCK_KEY_PREFIX . $key;
	}
	
	/**
	 * @param string $key
	 * @return NULL|kLock
	 */
	static public function create($key)
	{
		$store = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_LOCK_KEYS);
		if (!$store)
			return null;
		
		return new kLock($store, $key);
	}
	
	/**
	 * @param float $lockGrabTimeout
	 * @param int $lockHoldTimeout
	 * @return boolean
	 */
	public function lock($lockGrabTimeout = 2, $lockHoldTimeout = 5)
	{
		KalturaLog::log("Grabbing lock [{$this->key}]");

		$retryTimeout = microtime(true) + $lockGrabTimeout;
		while (microtime(true) < $retryTimeout)
		{
			if (!$this->store->add($this->key, true, $lockHoldTimeout))
			{
				usleep(self::LOCK_GRAB_TRY_INTERVAL);
				continue;
			}
			
			KalturaLog::log("Lock grabbed [{$this->key}]");
			return true;
		}

		KalturaLog::log("Lock grab timed out [{$this->key}]");
		return false;
	}
	
	public function unlock()
	{
		KalturaLog::log("Releasing lock [{$this->key}]");
		$this->store->delete($this->key);
		KalturaLog::log("Lock released [{$this->key}]");
	}
	
	/**
	 * @param callback $callback
	 * @param array $params
	 * @param float $lockGrabTimeout
	 * @param int $lockHoldTimeout
	 * @return mixed
	 */
	public function runLockedImpl($callback, array $params = array(), $lockGrabTimeout = 2, $lockHoldTimeout = 5)
	{
		if (!$this->lock($lockGrabTimeout, $lockHoldTimeout))
			throw new kCoreException("Timed out grabbing [{$this->key}]", kCoreException::LOCK_TIMED_OUT);
		
		try
		{
			$result = call_user_func_array($callback, $params);
		}
		catch (Exception $e)
		{
			$this->unlock();
			throw $e;
		}
		$this->unlock();
		return $result;			
	}	

	/**
	 * @param string $key
	 * @param callback $callback
	 * @param array $params
	 * @param float $lockGrabTimeout
	 * @param int $lockHoldTimeout
	 * @return mixed
	 */
	static public function runLocked($key, $callback, array $params = array(), $lockGrabTimeout = 2, $lockHoldTimeout = 5)
	{
		$lock = self::create($key);
		if (!$lock)
			return call_user_func_array($callback, $params);
			
		return $lock->runLockedImpl($callback, $params, $lockGrabTimeout, $lockHoldTimeout);
	}	
}
