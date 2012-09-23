<?php

require_once(dirname(__FILE__) . '/kBaseCacheWrapper.php');

/**
 * @package infra
 * @subpackage cache
 */
class kMemcacheCacheWrapper extends kBaseCacheWrapper
{
	const MAX_CONNECT_ATTEMPTS = 4;
	
	const COMPRESSED = 1;

	protected $hostName;
	protected $port;
	protected $flags;

	protected $memcache = null;
	protected $gotError = false;
	protected $connectAttempts = 0;
	
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
		
		$this->hostName = $hostName;
		$this->port = $port;
		$this->flags = ($flags == self::COMPRESSED ? MEMCACHE_COMPRESSED : 0);
		
		return $this->reconnect();
	}
	
	/**
	 * @return bool false on error
	 */
	protected function reconnect()
	{
		$this->memcache = null;
		
		if ($this->connectAttempts >= self::MAX_CONNECT_ATTEMPTS)
		{
			return false;
		}

		$connectResult = false;
		$connStart = microtime(true);
		while ($this->connectAttempts < self::MAX_CONNECT_ATTEMPTS)
		{
			$this->connectAttempts++;
			
			$memcache = new Memcache;	
			
			//$memcache->setOption(Memcached::OPT_BINARY_PROTOCOL, true);			// TODO: enable when moving to memcached v1.3

			$curConnStart = microtime(true);
			$connectResult = @$memcache->connect($this->hostName, $this->port);
			if ($connectResult || microtime(true) - $curConnStart < .5)		// retry only if there's an error and it's a timeout error
				break;

			self::safeLog("got timeout error while connecting to memcache...");
		}

		self::safeLog("connect took - ". (microtime(true) - $connStart). " seconds to {$this->hostName}:{$this->port} attempts {$this->connectAttempts}");

		if (!$connectResult)
		{
			self::safeLog("failed to connect to memcache");
			return false;
		}
		
		$this->memcache = $memcache;
		return true;
	}
	
	/**
	 * @param int $errno
	 * @param string $errstr
	 * @return bool
	 */
	public function errorHandler($errno, $errstr)
	{
		self::safeLog("got error from memcache [$errno] [$errstr]");
		$this->gotError = true;
		return false;
	}
	
	/**
	 * @param string $methodName
	 * @param array $params
	 * @return mixed false on error
	 */
	protected function callAndDetectErrors($methodName, $params)
	{
		while ($this->memcache)
		{
			$this->gotError = false;
			
			set_error_handler(array($this, 'errorHandler'));
			$result = call_user_func_array(array($this->memcache, $methodName), $params);
			restore_error_handler();
			
			if (!$this->gotError)
			{
				return $result;
			}
			
			$this->reconnect();
		}
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::get()
	 */
	public function get($key)
	{
		return $this->callAndDetectErrors('get', array($key));
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::set()
	 */
	public function set($key, $var, $expiry = 0)
	{
		return $this->callAndDetectErrors('set', array($key, $var, $this->flags, $expiry));
	}

	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::multiGet()
	 */
	public function multiGet($keys)
	{
		return $this->callAndDetectErrors('get', array($keys));
	}

	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::delete()
	 */
	public function delete($key)
	{
		return $this->callAndDetectErrors('delete', array($key));
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::increment()
	 */
	public function increment($key, $delta = 1)
	{
		return $this->callAndDetectErrors('increment', array($key, $delta));
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::decrement()
	 */
	public function decrement($key, $delta = 1)
	{
		return $this->callAndDetectErrors('decrement', array($key, $delta));
	}
}
