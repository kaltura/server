<?php
require_once __DIR__ . '/kBaseConfCache.php';
require_once __DIR__ . '/kMapCacheInterface.php';
require_once __DIR__ . '/kKeyCacheInterface.php';

class kApcConf extends kBaseConfCache implements kMapCacheInterface , kKeyCacheInterface
{
	protected $reloadFile;
	protected $apcFunctionsExist;

	public function __construct()
	{
		$this->apcFunctionsExist = function_exists('apc_fetch');
		$this->reloadFile = kEnvironment::get('cache_root_path').'/base.reload';
		parent::__construct();
	}

	protected function isReloadFileExist()
	{
		return file_exists($this->reloadFile);
	}

	public function delete($mapName)
	{
		if($this->apcFunctionsExist)
			return apc_delete($mapName);
	}

	public function load($key, $mapName)
	{
		if($this->apcFunctionsExist)
		{
			$mapStr = apc_fetch($mapName);
			$map = json_decode($mapStr,true);
			if ($map && $this->validateMap($map, $mapName, $key))
			{
				$this->removeKeyFromMap($map);
				return $map;
			}
		}
		return null;
	}

	public function store($key, $mapName, $map, $ttl=0)
	{
		if($this->apcFunctionsExist && PHP_SAPI != 'cli')
		{
			$this->addKeyToMap($map, $mapName, $key);
			$mapStr = json_encode($map);
			return apc_store($mapName, $mapStr, $ttl);
		}
		return false;
	}

	public function loadKey()
	{
		if($this->apcFunctionsExist && !$this->isReloadFileExist())
			return apc_fetch(kBaseConfCache::CONF_CACHE_VERSION_KEY);

		if($this->isReloadFileExist())
		{
			$deleted = @unlink($this->reloadFile);
			error_log('Base configuration reloaded');
			if(!$deleted)
				error_log('Failed to delete base.reload file');
		}
		return null;
	}

	public function storeKey($key, $ttl=30)
	{
			if($this->apcFunctionsExist && PHP_SAPI != 'cli')
				return apc_store(kBaseConfCache::CONF_CACHE_VERSION_KEY, $key, $ttl);
	}

	public function isKeyRequired()
	{
		return true;
	}
}
