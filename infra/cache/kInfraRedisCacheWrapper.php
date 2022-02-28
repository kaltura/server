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
	protected $timeout;
	protected $statsKey;

	protected $redis = null;
	protected $gotError = false;
	protected static $_stats = array();
	protected $connectAttempts = 0;
	protected $persistent = false;
	protected $cluster = false;

	const STAT_CONN = 'conn';
	const STAT_OP = 'op';
	const STAT_COUNT = 'count';
	const STAT_TIME = 'time';
	const MAX_CONNECT_ATTEMPTS = 4;

	/* (non-PHPdoc)
     * @see kBaseCacheWrapper::doInit()
     */
	protected function doInit($config)
	{
		if (!class_exists('Redis'))
		{
			self::safeLog("Redis class doesn't exists, can't connect without it");
			return false;
		}

		if (!isset($config['host']) || !isset($config['port']))
		{
			self::safeLog("Missing host or port in config, can't connect without it");
			return false;
		}

		$this->hostName = $config['host'];
		$this->port = $config['port'];
		$this->timeout = $config['timeout'];
		if (isset($config['persistent']) && $config['persistent'])
			$this->persistent = true;
		if (isset($config['cluster']) && $config['cluster'])
			$this->cluster = true;

		return $this->reconnect();
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
	protected function doGet($key)
	{
		return $this->callAndDetectErrors('get', array($key));
	}

	/* (non-PHPdoc)
     * @see kBaseCacheWrapper::set()
     */
	protected function doSet($key, $var, $expiry = 0)
	{
		if($expiry>0)
		{
			return $this->callAndDetectErrors('setex', array($key, $expiry, $var));
		}
		else
		{
			return $this->callAndDetectErrors('set', array($key, $var));
		}
	}

	/* (non-PHPdoc)
     * @see kBaseCacheWrapper::add()
     */
	protected function doAdd($key, $var, $expiry = 0)
	{
		$res = $this->callAndDetectErrors('setnx', array($key, $var));
		if($res)
		{
			$this->callAndDetectErrors('expire', array($key, $expiry));
		}
		return $res;
	}

	/* (non-PHPdoc)
     * @see kBaseCacheWrapper::delete()
	 */
	protected function doDelete($key)
	{
		return $this->callAndDetectErrors('del', array($key));
	}

	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::increment()
	 */
	public function doIncrement($key, $delta = 1)
	{
		return $this->callAndDetectErrors('incrby', array($key, $delta));
	}

	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::decrement()
	 */
	public function doDecrement($key, $delta = 1)
	{
		return $this->callAndDetectErrors('decrby', array($key, $delta));
	}

	/* (non-PHPdoc)
    * @see kBaseCacheWrapper::multiGet()
    */
	public function doMultiGet($keys)
	{
		return $this->callAndDetectErrors('mget', array($keys));
	}
	
	public function doZadd($key, $value, $member)
	{
		return $this->callAndDetectErrors('zadd', array($key, $value, $member));
	}
	
	public function doZrem($key, $member)
	{
		return $this->callAndDetectErrors('zrem', array($key, $member));
	}
	
	public function doZrevrange($key, $low, $high)
	{
		return $this->callAndDetectErrors('zrevrange', array($key, $low, $high, true));
	}
	
	public function doZrevrank($key, $member)
	{
		return $this->callAndDetectErrors('zrevrank', array($key, $member));
	}
	
	public function doZscore($key, $member)
	{
		return $this->callAndDetectErrors('zscore', array($key, $member));
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

	protected function callAndDetectErrors($methodName, $params)
	{
		kApiCache::disableConditionalCache();
		while ($this->redis)
		{
			$this->gotError = false;

			set_error_handler(array($this, 'errorHandler'));
			$start = microtime(true);
			$result = call_user_func_array(array($this->redis, $methodName), $params);
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
			$curConnStart = microtime(true);
			try
			{
				if (!$this->cluster)
				{
					$redis = new Redis();

					if ($this->persistent)
						$redis->pconnect($this->hostName, $this->port, $this->timeout);
					else
						$redis->connect($this->hostName, $this->port, $this->timeout);

					$connectResult = $redis->isConnected();
				}
				else
				{
					$redis = new RedisCluster(null, array($this->hostName.":".$this->port), $this->timeout, $this->timeout, $this->persistent);
					
					//There is no isConnected in cluster mode so we need to verify the object is not null to make sure the connection was successful.
					$connectResult = $redis ? true : false;
				}

			}
			catch (Exception $e)
			{
				self::safeLog("failed to connect to redis");
			}

			if ($connectResult || microtime(true) - $curConnStart < .5)        // retry only if there's an error and it's a timeout error
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

		$this->connectAttempts = 0;
		$this->redis = $redis;
		return true;
	}
}