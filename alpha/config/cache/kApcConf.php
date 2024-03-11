<?php
require_once __DIR__ . "/../../../infra/cache/kApcWrapper.php";
require_once __DIR__ . '/kBaseConfCache.php';
require_once __DIR__ . '/kMapCacheInterface.php';
require_once __DIR__ . '/kKeyCacheInterface.php';

class kApcConf extends kBaseConfCache implements kMapCacheInterface , kKeyCacheInterface
{
	protected $reloadFileExist;
	protected $apcFunctionsExist;

	public function __construct()
	{
		$reloadFile = kEnvironment::get('cache_root_path').'base.reload';
		$this->apcFunctionsExist = kApcWrapper::functionExists('fetch');
		$this->reloadFileExist = file_exists($reloadFile);
		if($this->reloadFileExist)
		{
			$deleted = @unlink($reloadFile);
			error_log('Base configuration reloaded');
			if(!$deleted)
				error_log('Failed to delete base.reload file');
		}

		parent::__construct();
	}

	public function delete($mapName)
	{
		if($this->apcFunctionsExist)
			return kApcWrapper::apcDelete(self::CONF_MAP_PREFIX.$mapName);
	}

	public function load($key, $mapName)
	{
		if($this->apcFunctionsExist && !$this->reloadFileExist)
		{
			$mapStr = kApcWrapper::apcFetch(self::CONF_MAP_PREFIX.$mapName);
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
			return kApcWrapper::apcStore(self::CONF_MAP_PREFIX.$mapName, $mapStr, $ttl);
		}
		return false;
	}

	public function loadKey()
	{
		if($this->apcFunctionsExist && !$this->reloadFileExist)
			return kApcWrapper::apcFetch(kBaseConfCache::CONF_CACHE_VERSION_KEY);

		return null;
	}

	public function storeKey($key, $ttl=30)
	{
		if($this->apcFunctionsExist && PHP_SAPI != 'cli')
		{
			$existingKey = kApcWrapper::apcFetch(kBaseConfCache::CONF_CACHE_VERSION_KEY);
			if(!$existingKey || strcmp($existingKey, $key))
			{
				return kApcWrapper::apcStore(kBaseConfCache::CONF_CACHE_VERSION_KEY, $key, $ttl);
			}
		}
		return null;
	}

	public function isKeyRequired()
	{
		return true;
	}
}
