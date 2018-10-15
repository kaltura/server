<?php
require_once __DIR__ . '/kBaseMemcacheConf.php';

class kLocalMemCacheConf extends kBaseMemcacheConf
{
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

