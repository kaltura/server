<?php

require_once(dirname(__FILE__) . '/kInfraBaseCacheWrapper.php');

/**
 * @package infra
 * @subpackage cache
 */
class kInfraRedisCacheWrapper extends kInfraBaseCacheWrapper
{
	protected $hostName;
	protected $port;
	protected $scheme;
	protected $timeout;
	protected $statsKey;

	protected $redis = null;
	protected $gotError = false;
	protected static $_stats = array();
	protected $connectAttempts = 0;

	const STAT_CONN = 'conn';
	const STAT_OP = 'op';
	const STAT_COUNT = 'count';
	const STAT_TIME = 'time';
	const MAX_CONNECT_ATTEMPTS = 4;

	/* (non-PHPdoc)
     * @see kBaseCacheWrapper::doInit()
     */
	protected function doInit ($config)
	{
		if (!isset($config['host']) || !isset($config['port']))
		{
			self::safeLog("Missing host or port in config, can't connect without it");
			return false;
		}

		require './../../vendor/predis/autoload.php';

		$this->scheme = $config['scheme'];
		$this->hostName = $config['host'];
		$this->port = $config['port'];
		$this->timeout = $config['timeout'];

		$redis = new Redis();
		try
		{
			$redis->connect($this->hostName,$this->port,$this->timeout);
		}
		catch(Exception $e)
		{
			self::safeLog("failed to connect to redis");
		}

		$connection = $redis->isConnected();

		if (!$connection)
		{
			self::safeLog("failed to connect to redis");
			return false;
		}

		$this-> redis = $redis;
		return true;
	}

	public function __destruct()
	{
		$this->close();
	}

	protected function close()
	{
		if ($this->redis)
		{
			$this->redis->close();
		}
		$this->redis = null;
	}

	/* (non-PHPdoc)
     * @see kBaseCacheWrapper::get()
     */
	protected function doGet ($key)
	{
		return $this->callAndDetectErrors(function($key) {
			return $this->redis->get($key);
		}, array($key));
	}

	/* (non-PHPdoc)
     * @see kBaseCacheWrapper::set()
     */
	protected function doSet ($key, $var, $expiry = 0)
	{
		return $this->callAndDetectErrors(function($key, $var, $expiry = 0) {
			return $this->redis->setex($key, $expiry, $var);
		}, array($key, $var, $expiry));
	}

	/* (non-PHPdoc)
     * @see kBaseCacheWrapper::add()
     */
	protected function doAdd ($key, $var, $expiry = 0)
	{
		return $this->callAndDetectErrors(function($key, $var, $expiry = 0) {
			$res =  $this->redis->setnx($key, $var);
			if($res)
			{
				$this->redis->expire($key, $expiry);
			}
			return $res;
		}, array($key, $var, $expiry));
	}

	/* (non-PHPdoc)
     * @see kBaseCacheWrapper::delete()
	 */
	protected function doDelete ($key)
	{
		return $this->callAndDetectErrors(function($key) {
			return $this->redis->del($key);
		}, array($key));
	}

	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::increment()
	 */
	public function doIncrement($key, $delta = 1)
	{
		return $this->callAndDetectErrors(function($key, $delta = 1) {
			return $this->redis->incrby($key, $delta);
		}, array($key, $delta));
	}

	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::decrement()
	 */
	public function doDecrement($key, $delta = 1)
	{
		return $this->callAndDetectErrors(function($key, $delta = 1) {
			return $this->redis->decrby($key, $delta);
		}, array($key, $delta));
	}

	/* (non-PHPdoc)
    * @see kBaseCacheWrapper::multiGet()
    */
	public function doMultiGet($keys)
	{
		return $this->callAndDetectErrors(function($keys) {
			return $this->redis->mget($keys);
		}, array($keys));
	}

	public static function sendMonitorEvents()
	{
		foreach (self::$_stats as $statsKey => $typeStats)
		{
			foreach ($typeStats as $type => $stats)
			{
				switch ($type)
				{
					case self::STAT_CONN:
						KalturaMonitorClient::monitorConnTook($statsKey, $stats[self::STAT_TIME], $stats[self::STAT_COUNT]);
						break;

					case self::STAT_OP:
						KalturaMonitorClient::monitorRedisAccess($statsKey, $stats[self::STAT_TIME], $stats[self::STAT_COUNT]);
						break;
				}
			}
		}

		self::$_stats = array();
	}

	public static function outputStats()
	{
		foreach (self::$_stats as $statsKey => $typeStats)
		{
			$cur = 'instance:' . $statsKey;
			foreach ($typeStats as $type => $stats)
			{
				foreach ($stats as $key => $value)
				{
					$cur .= ', ' . $type . '_' . $key . ':' . $value;
				}
			}

			KalturaLog::log($cur);
		}
	}

	protected function updateStats($type, $stats)
	{
		if (!isset(self::$_stats[$this->statsKey]))
		{
			self::$_stats[$this->statsKey] = array();
		}
		$typeStats = &self::$_stats[$this->statsKey];

		if (!isset($typeStats[$type]))
		{
			$typeStats[$type] = array();
		}

		foreach ($stats as $key => $value)
		{
			if (isset($typeStats[$type][$key]))
			{
				$typeStats[$type][$key] += $value;
			}
			else
			{
				$typeStats[$type][$key] = $value;
			}
		}
	}

	/**
	 * @param int $errno
	 * @param string $errstr
	 * @return bool
	 */
	public function errorHandler($errno, $errstr)
	{
		self::safeLog("got error from redis [$errno] [$errstr]");
		$this->gotError = true;
		return false;
	}

	protected function callAndDetectErrors($method, $params)
	{
		while ($this->redis)
		{
			$this->gotError = false;

			set_error_handler(array($this, 'errorHandler'));
			$start = microtime(true);
			$result = $method(...$params);
			$end = microtime(true);
			restore_error_handler();

			$this->updateStats(self::STAT_OP, array(
				self::STAT_COUNT => 1,
				self::STAT_TIME => $end - $start));

			if (!$this->gotError)
			{
				return $result;
			}
			$this->reconnect();
		}

		self::safeLog("There is no active redis connection");
		return false;
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

			$redis = new Redis();
			$curConnStart = microtime(true);
			try
			{
				$redis->connect($this->hostName,$this->port,$this->timeout);
			}
			catch(Exception $e)
			{
				self::safeLog("failed to connect to redis");
			}

			$connectResult = $redis->isConnected();
			if ($connectResult || microtime(true) - $curConnStart < .5)		// retry only if there's an error and it's a timeout error
				break;

			self::safeLog("got timeout error while connecting to redis...");
		}

		$connTook = microtime(true) - $connStart;
		self::safeLog("connect took - {$connTook} seconds to {$this->hostName}:{$this->port} attempts {$this->connectAttempts}");

		$this->updateStats(self::STAT_CONN, array(
			self::STAT_COUNT => 1,
			self::STAT_TIME => $connTook));

		if (!$connectResult)
		{
			self::safeLog("failed to connect to redis");
			return false;
		}

		$this->redis = $redis;
		return true;
	}
}