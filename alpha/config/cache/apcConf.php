<?php
require_once __DIR__."/baseConfCache.php";
require_once __DIR__."/mapCacheInterface.php";
require_once __DIR__."/keyCacheInterface.php";

class apcConf extends baseConfCache implements mapCacheInterface , keyCacheInterface
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
			$map = apc_fetch($mapName);
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
			return apc_store($mapName, $map, $ttl);
		}
		return false;
	}
	public function loadKey()
	{
		if($this->apcFunctionsExist && !$this->isReloadFileExist())
			return apc_fetch(baseConfCache::CONF_CACHE_VERSION_KEY);

		if($this->isReloadFileExist())
		{
			$deleted = @unlink($this->reloadFile);
			error_log("Base configuration reloaded");
			if(!$deleted)
				error_log("Failed to delete base.reload file");
		}
		return null;
	}
	public function storeKey($key, $ttl=30)
	{
			if($this->apcFunctionsExist && PHP_SAPI != 'cli')
				return apc_store(baseConfCache::CONF_CACHE_VERSION_KEY, $key, $ttl);
	}
	public function isKeyRequired()
	{
		return true;
	}
}
