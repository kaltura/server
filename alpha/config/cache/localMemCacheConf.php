<?php
require_once __DIR__.'/baseMemcacheConf.php';

class localMemCacheConf extends baseMemcacheConf
{
	function __construct()
	{
		$confParams = parent::getConfigParams('localMemCacheConf');
		if($confParams)
		{
			$port = $confParams['port'];
			$host = $confParams['host'];
			return  parent::__construct($port, $host);
		}
		$this->cache=null;
	}
	public function isKeyRequired(){ return true;}

	public function load($key, $mapName)
	{
		$map = parent::load($key, $mapName);
		if ($map && $this->validateMap($map, $mapName, $key))
		{
			$this->removeKeyFromMap($map);
			return $map;
		}
		return null;
	}

	public function store($key, $mapName, $map, $ttl=0)
	{
		if(PHP_SAPI != 'cli')
		{
			$this->addKeyToMap($map, $mapName, $key);
			return parent::store($key, $mapName, $map);
		}
	}
}

