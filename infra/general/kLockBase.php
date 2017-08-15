<?php
/**
 * @package Core
 * @subpackage utils
 */
class kLockBase
{
	const LOCK_KEY_PREFIX = '__LOCK';
	const LOCK_TRY_INTERVAL = 20000;
	
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
	public function __construct($store, $key)
	{
		$this->store = $store;
		$this->key = self::LOCK_KEY_PREFIX . $key;
	}
	
	/**
	 * @param float $lockGrabTimeout
	 * @param int $lockHoldTimeout
	 * @return boolean
	 */
	public function lock($lockGrabTimeout = 2, $lockHoldTimeout = 5)
	{
		self::safeLog("Grabbing lock [{$this->key}]");

		$retryTimeout = microtime(true) + $lockGrabTimeout;
		while (microtime(true) < $retryTimeout)
		{
			if (!$this->store->add($this->key, true, $lockHoldTimeout))
			{
				usleep(self::LOCK_TRY_INTERVAL);
				continue;
			}
			
			self::safeLog("Lock grabbed [{$this->key}]");
			return true;
		}

		self::safeLog("Lock grab timed out [{$this->key}]");
		return false;
	}
	
	public function unlock($lockReleaseTimeout = 2)
	{
		self::safeLog("Releasing lock [{$this->key}]");

		$retryTimeout = microtime(true) + $lockReleaseTimeout;
		while (microtime(true) < $retryTimeout)
		{
			if (!$this->store->delete($this->key))
			{
				usleep(self::LOCK_TRY_INTERVAL);
				continue;
			}

			self::safeLog("Lock released [{$this->key}]");
			return true;
		}

		self::safeLog("Lock released failed for [{$this->key}]");
		return false;
	}

	/**
	 * This function is required since this code can run before the autoloader
	 *
	 * @param string $msg
	 */
	protected static function safeLog($msg)
	{
		if (class_exists('KalturaLog'))
			KalturaLog::log($msg);
	}

	/**
	 * @param string $key
	 * @return kLockBase
	 */
	static public function grabLocalLock($key)
	{ 
		if (!function_exists('apc_add'))
			return null;
		
		require_once(__DIR__ . '/../cache/kApcCacheWrapper.php');		// can be called before autoloader
		
		$lock = new kLockBase(new kApcCacheWrapper(), $key);
		if (!$lock->lock())
			return null;
		
		return $lock;
	}
}
