<?php
class baseConfCache
{
	const FULL_MAP_KEY = '__full';
	const  CONF_CACHE_VERSION_KEY = "CONF_CACHE_VERSION_KEY";
	const CONF_CACHE_KEY = "CONF_CACHE_KEY";
	const CONF_CACHE_KEY_PREFIX = "CONF-";
	private $usageCounter;
	private $cacheMissCounter;
	private $usageMap;
	private $keyUsageCounter;

	function __construct()
	{
		$this->usageCounter = 0;
		$this->usageMap = array();
		$this->keyUsageCounter = 0;
		$this->cacheMissCounter = 0;
	}

	protected function getCacheKey($key, $mapName)
	{
		return $key . '-' . $mapName;
	}

	public function orderMap(&$mapsList)
	{
		return usort($mapsList, function ($a, $b)
		{
/*			if(!strpos($a, 'hosts'))
				return -1;
			if(!strpos($b, 'hosts'))
				return 1;
*/
			$val = strlen($a) - strlen($b);
			if (!$val)
				$val = strpos($b, '#') - strpos($a, '#');

			return $val;
		});
	}

	public function validateMap($map, $mapName, $key)
	{
		if (!$key)
			return true;
		$cacheKey = $this->getCacheKey($key, $mapName);
		if(isset($map['CONF_CACHE_KEY']))
			if(strcmp($map['CONF_CACHE_KEY'], $cacheKey))
				return false;
		return true;
	}

	public function addKeyToMap(&$map, $mapName, $key)
	{
		if (!isset($map[self::CONF_CACHE_KEY]))
		{
			$cacheKey = $this->getCacheKey($key, $mapName);
			$map[self::CONF_CACHE_KEY] = $cacheKey;
		}
	}

	public function removeKeyFromMap(&$map)
	{
		if (isset($map[self::CONF_CACHE_KEY]))
		{
			unset($map[self::CONF_CACHE_KEY]);
		}
	}

	public function incUsage($mapName)
	{
		$this->usageCounter++;
		isset($this->usageMap[$mapName]) ? $this->usageMap[$mapName]++ : $this->usageMap[$mapName] = 0;
	}

	public function generateKey()
	{
		$fileHash = md5(realpath(__file__));
		$cacheVersion = substr(time(), -6) . substr($fileHash, 0, 4);
		return baseConfCache::CONF_CACHE_KEY_PREFIX . $cacheVersion;
	}

	public function hasMap($key, $mapName)
	{
		$ret = $this->load($key, $mapName);
		return count($ret);
	}

	public function getHostName ()
	{
		return isset($_SERVER["HOSTNAME"]) ? $_SERVER["HOSTNAME"] : gethostname();
	}
	public function incKeyUsageCounter(){$this->keyUsageCounter++;}
	public function getKeyUsageCounter(){return $this->keyUsageCounter;}
	public function incCacheMissCounter(){$this->cacheMissCounter++;}
	public function getCacheMissCounter(){return $this->cacheMissCounter;}
	public function getUsageCounter(){return $this->usageCounter;}
	public function getUsageMap(){return $this->usageMap;}
	public function isKeyRequired(){ return false;}
	public function storeKey($key,$ttl=30){return;}
	public function loadKey(){return false;}
}
