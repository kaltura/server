<?php

require_once(dirname(__FILE__) . '/kBaseCacheWrapper.php');

/**
 * @package infra
 * @subpackage cache
 */
class kMemcacheCacheWrapper extends kBaseCacheWrapper
{
	protected $memcache;
	protected $flags;
	
	/**
	 * @param string $hostName
	 * @param int $port
	 * @param int $flags
	 * @return bool false on error
	 */
	public function init($hostName, $port, $flags)
	{
		if (!class_exists('Memcache'))
		{
			return false;
		}
		
		$connStart = microtime(true);
		
		for($i = 0; $i < 3; $i++)
		{
			$memcache = new Memcache;	
			
			//$memcache->setOption(Memcached::OPT_BINARY_PROTOCOL, true);			// TODO: enable when moving to memcached v1.3

			$curConnStart = microtime(true);
			$res = @$memcache->connect($hostName, $port);
			if ($res || microtime(true) - $curConnStart < .5)		// retry only if there's an error and it's a timeout error
				break;

			self::safeLog("got timeout error, retrying...");
		}

		self::safeLog("connect took - ". (microtime(true) - $connStart). " seconds to $hostName:$port");

		if (!$res)
		{
			self::safeLog("failed to connect to global memcache");
			return false;
		}
		
		$this->memcache = $memcache;
		$this->flags = $flags;
		return true;
	}	
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::get()
	 */
	public function get($key, $defaultExpiry = 0)
	{
		return $this->memcache->get($key);
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::set()
	 */
	public function set($key, $var, $expiry = 0, $defaultExpiry = 0)
	{
		return $this->memcache->set($key, $var, $this->flags, $expiry);
	}

	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::multiGet()
	 */
	public function multiGet($keys, $defaultExpiry = 0)
	{
		return $this->memcache->get($keys);
	}
}

