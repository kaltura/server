<?php

/**
 * Class kBatchUtils
 */
class kBatchUtils
{
	/**
	 * @param $param
	 * @param string $mapName
	 * @param null $defaultValue
	 * @return bool|mixed|null
	 * @throws Exception
	 */
	public static function getKconfParam($param, $mapName ='local', $defaultValue = false)
	{
		if (class_exists('KBatchBase'))
		{
			$config = self::tryLoadKconfConfig($mapName, true);
			if (!$config || !isset($config[$param]))
			{
				return $defaultValue;
			}
			return $config[$param];
		}
		else
		{
			return kConf::get($param, $mapName, $defaultValue);
		}
	}

	/**
	 * @param bool $useBatchClient
	 * @return array
	 * @throws Exception
	 */
	public static function tryLoadSharedStorageKconfConfig($useBatchClient = false)
	{
		$cloudStorage = self::tryLoadKconfConfig("cloud_storage", $useBatchClient);
		$runtimeConfig = self::tryLoadKconfConfig("runtime_config", $useBatchClient);

		$s3Arn = isset($cloudStorage['s3Arn']) ? $cloudStorage['s3Arn'] : null;
		$storageOptions = isset($cloudStorage['storage_options']) ? $cloudStorage['storage_options'] : array();
		$storageTypeMap = isset($cloudStorage['storage_type_map']) ? $cloudStorage['storage_type_map'] : array();
		$ffmpegReconnectParams = isset($runtimeConfig['ffmpeg_reconnect_params']) ? $runtimeConfig['ffmpeg_reconnect_params'] : null;

		$sharedStorageConfig = array(
			's3Arn' => $s3Arn,
			'storageTypeMap' => $storageTypeMap,
			'ffmpegReconnectParams' => $ffmpegReconnectParams,
			's3Region' => isset($storageOptions['s3Region']) ? $storageOptions['s3Region'] : null,
		);

		$sharedStorageConfig['endPoint'] = isset($storageOptions['endPoint']) ? $storageOptions['endPoint'] : null;
		$sharedStorageConfig['accessKeyId'] = isset($storageOptions['accessKeyId']) ? $storageOptions['accessKeyId'] : null;
		$sharedStorageConfig['accessKeySecret'] = isset($storageOptions['accessKeySecret']) ? $storageOptions['accessKeySecret'] : null;
		$sharedStorageConfig['concurrency'] = isset($storageOptions['concurrency']) ? $storageOptions['concurrency'] : null;
		$sharedStorageConfig['maxConcurrentUploadConnections'] = isset($storageOptions['maxConcurrentUploadConnections']) ? $storageOptions['maxConcurrentUploadConnections'] : null;

		self::setStorageRunParams($sharedStorageConfig);

		return $sharedStorageConfig;
	}

	/**
	 * @param $mapName
	 * @param bool $useBatchClient
	 * @return array|mixed
	 * @throws Exception
	 */
	public static function tryLoadKconfConfig($mapName, $useBatchClient = false )
	{
		$configCacheFileName = kEnvironment::get('cache_root_path') . DIRECTORY_SEPARATOR . 'batch' . DIRECTORY_SEPARATOR . "confMaps" . DIRECTORY_SEPARATOR . "$mapName.txt";
		if(!kFile::checkFileExists($configCacheFileName))
		{
			return self::loadAndSaveKconfConfig($configCacheFileName, $mapName, $useBatchClient);
		}
		
		$configData = unserialize(kFile::getFileContent($configCacheFileName));
		if(time() > $configData['expirationTime'])
		{
			KalturaLog::debug("Config cache file no longer valid, Will reload config");
			$configData = self::loadAndSaveKconfConfig($configCacheFileName, $mapName, $useBatchClient);
		}
		else
		{
			KalturaLog::debug("Config cache file valid, returning cached config");
		}
		return $configData;
	}

	/**
	 * @param $configCacheFileName
	 * @param $mapName
	 * @param bool $useBatchClient
	 * @return array|mixed
	 */
	public static function loadAndSaveKconfConfig($configCacheFileName, $mapName, $useBatchClient = false)
	{
		if ($useBatchClient)
		{
			$map = self::getConfigMap($mapName);
		}
		else
		{
			$map = kConf::getMap($mapName);
		}
		$expirationTime = isset($map['static_file_cache_expirtation_time']) ? $map['static_file_cache_expirtation_time'] : 120;
		$map['expirationTime'] = time() + $expirationTime;
		KalturaLog::debug("Config loaded: " . print_r($map, true));
		kFile::safeFilePutContents($configCacheFileName, serialize($map));
		kCacheConfFactory::close();
		return $map;
	}

	/**
	 * @return array
	 */
	private static function loadConfFromKConf()
	{
		$cloudStorageConfig = kConf::getMap('cloud_storage');
		$runtimeConfig = kConf::getMap('runtime_config');
		return array($cloudStorageConfig, $runtimeConfig);
	}

	/**
	 * @return array
	 */
	private static function loadConfFromApi()
	{

		$cloudStorageConfig = self::getConfigMap('cloud_storage');
		$runtimeConfig = self::getConfigMap('runtime_config');
		return array($cloudStorageConfig, $runtimeConfig);
	}


	/**
	 * @param $storageRunParams
	 */
	private static function setStorageRunParams($storageRunParams)
	{
		kSharedFileSystemMgr::setFileSystemOptions('s3Arn', $storageRunParams['s3Arn']);
		kSharedFileSystemMgr::setFileSystemOptions('s3Region', $storageRunParams['s3Region']);

		if(isset($storageRunParams['endPoint'])) {
			kSharedFileSystemMgr::setFileSystemOptions('endPoint', $storageRunParams['endPoint']);
		}

		if(isset($storageRunParams['accessKeyId'])) {
			kSharedFileSystemMgr::setFileSystemOptions('accessKeyId', $storageRunParams['accessKeyId']);
		}

		if(isset($storageRunParams['accessKeySecret'])) {
			kSharedFileSystemMgr::setFileSystemOptions('accessKeySecret', $storageRunParams['accessKeySecret']);
		}

		$storageTypeMap = $storageRunParams['storageTypeMap'];
		foreach ($storageTypeMap as $key => $value) {
			kFile::setStorageTypeMap($key, $value);
		}
	}

	/**
	 * @param $pattern
	 * @param $fileCmd
	 * @param $cmdLine
	 * @throws Exception
	 */
	public static function addReconnectParams($pattern, $fileCmd, &$cmdLine)
	{
		if (strpos($fileCmd, $pattern) !== 0) {
			return;
		}

		$ffmpegReconnectParams = self::getKconfParam('ffmpegReconnectParams', 'runtime_config');

		if ($ffmpegReconnectParams) {
			$cmdLine .= " $ffmpegReconnectParams";
		}
	}

	/**
	 * @param $key
	 * @param string $mapName
	 * @param null $defaultValue
	 */
	public static function getConfigMap($mapName)
	{
		$configArray = array();
		$configurationPluginClient = KalturaConfMapsClientPlugin::get(kBatchBase::$kClient);
		$configurationMapFilter = new KalturaConfMapsFilter();
		$configurationMapFilter->nameEqual = $mapName;
		$configurationMapFilter->relatedHostEqual = kBatchBase::$taskConfig->getSchedulerName();
		$configurationMap = $configurationPluginClient->confMaps->get($configurationMapFilter);
		if ($configurationMap)
		{
			$configArray = json_decode($configurationMap->content, true);
		}
		return $configArray;
	}
}