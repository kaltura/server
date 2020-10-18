<?php

require_once(dirname(__FILE__) . '/kInfraBaseCacheWrapper.php');

/**
 * @package infra
 * @subpackage cache
 */
class kInfraMemcacheCacheWrapper extends kInfraBaseCacheWrapper
{
	const MAX_CONNECT_ATTEMPTS = 4;
	
	const COMPRESSED = 1;

	protected $hostName;
	protected $port;
	protected $flags = 0;
	protected $persistent = false;

	protected static $_stats = array();
	protected $stats;

	protected $memcache = null;
	protected $gotError = false;
	protected $connectAttempts = 0;
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::doInit()
	 */
	protected function doInit($config)
	{
		if (!class_exists('Memcache'))
		{
			return false;
		}
		
		$this->hostName = $config['host'];
		$this->port = $config['port'];
		if (isset($config['flags']) && $config['flags'] == self::COMPRESSED)
			$this->flags = MEMCACHE_COMPRESSED;
		if (isset($config['persistent']) && $config['persistent'])
			$this->persistent = true;

		$statsKey = $this->hostName . ':' . $this->port;
		if (!isset(self::$_stats[$statsKey]))
		{
			self::$_stats[$statsKey] = array();
		}
		$this->stats = &self::$_stats[$statsKey];

		return $this->reconnect();
	}
	
	public function __destruct()
	{
		$this->close();
	}

	protected function close()
	{
		if ($this->memcache)
		{
			$this->memcache->close();
		}
		$this->memcache = null;
	}

	/**
	 * @return bool false on error
	 */
	protected function reconnect()
	{
		$this->close();
		
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
			if ($this->persistent)
				$connectResult = @$memcache->pconnect($this->hostName, $this->port);
			else 
				$connectResult = @$memcache->connect($this->hostName, $this->port);
			if ($connectResult || microtime(true) - $curConnStart < .5)		// retry only if there's an error and it's a timeout error
				break;

			self::safeLog("got timeout error while connecting to memcache...");
		}

		$connTook = microtime(true) - $connStart;
		self::safeLog("connect took - {$connTook} seconds to {$this->hostName}:{$this->port} attempts {$this->connectAttempts}");

		$this->updateStats(array(
			'count' => 1,
			'time' => $connTook));

		if (class_exists("KalturaMonitorClient"))
			KalturaMonitorClient::monitorConnTook($this->hostName, $connTook);

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
			$start = microtime(true);
			$result = call_user_func_array(array($this->memcache, $methodName), $params);
			$end = microtime(true);
			restore_error_handler();

			$this->updateStats(array(
				'count' => 1,
				'time' => $end - $start));
			
			if (!$this->gotError)
			{
				return $result;
			}
			
			$this->reconnect();
		}

		self::safeLog("There isnt an active memcache connection");
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::get()
	 */
	protected function doGet($key)
	{
		return $this->callAndDetectErrors('get', array($key));
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::set()
	 */
	protected function doSet($key, $var, $expiry = 0)
	{
		return $this->callAndDetectErrors('set', array($key, $var, $this->flags, $expiry));
	}

	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::add()
	 */
	public function doAdd($key, $var, $expiry = 0)
	{
		return $this->callAndDetectErrors('add', array($key, $var, $this->flags, $expiry));
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::multiGet()
	 */
	public function doMultiGet($keys)
	{
		return $this->callAndDetectErrors('get', array($keys));
	}

	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::delete()
	 */
	public function doDelete($key)
	{
		return $this->callAndDetectErrors('delete', array($key));
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::increment()
	 */
	public function doIncrement($key, $delta = 1)
	{
		return $this->callAndDetectErrors('increment', array($key, $delta));
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::decrement()
	 */
	public function doDecrement($key, $delta = 1)
	{
		return $this->callAndDetectErrors('decrement', array($key, $delta));
	}

	protected function updateStats($stats)
	{
		foreach ($stats as $key => $value)
		{
			if (isset($this->stats[$key]))
			{
				$this->stats[$key] += $value;
			}
			else
			{
				$this->stats[$key] = $value;
			}
		}
	}

	public static function outputStats()
	{
		foreach (self::$_stats as $statsKey => $stats)
		{
			$cur = 'instance:' . $statsKey;
			foreach ($stats as $key => $value)
			{
				$cur .= ', ' . $key . ':' . $value;
			}

			KalturaLog::log($cur);
		}

		self::$_stats = array();
	}
}
