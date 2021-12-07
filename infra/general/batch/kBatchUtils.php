<?php

/**
 * Class kBatchUtils
 */
class kBatchUtils
{
	/**
	 * @param $param
	 * @param bool $useBatchClient
	 * @return mixed|null
	 * @throws Exception
	 */
	public static function getKconfParam($param, $useBatchClient = false, $defaultValue = null )
	{
		$chunkConfig = self::tryLoadKconfConfig($useBatchClient);
		if(!$chunkConfig || !isset($chunkConfig[$param])) {
			return $defaultValue;
		}
		
		return $chunkConfig[$param];
	}

	/**
	 * @param bool $useBatchClient
	 * @return array|mixed
	 * @throws Exception
	 */
	public static function tryLoadKconfConfig($useBatchClient = false)
	{
		$configCacheFileName = kEnvironment::get('cache_root_path') . DIRECTORY_SEPARATOR . 'batch' . DIRECTORY_SEPARATOR . 'sharedStorageConfig_serialized.txt';
		if(!kFile::checkFileExists($configCacheFileName))
		{
			$sharedStorageClientConfig = self::loadAndSaveKconfConfig($configCacheFileName, $useBatchClient);
			self::setStorageRunParams($sharedStorageClientConfig);
			return $sharedStorageClientConfig;
		}
		
		$sharedStorageClientConfig = unserialize(kFile::getFileContent($configCacheFileName));
		if(time() > $sharedStorageClientConfig['expirationTime'])
		{
			KalturaLog::debug("Config cache file no longer valid, Will reload config");
			$sharedStorageClientConfig = self::loadAndSaveKconfConfig($configCacheFileName, $useBatchClient);
			self::setStorageRunParams($sharedStorageClientConfig);
			return $sharedStorageClientConfig;
		}
		else
		{
			KalturaLog::debug("Config cache file valid, returning cached config");
		}
		
		self::setStorageRunParams($sharedStorageClientConfig);
		return $sharedStorageClientConfig;
	}

	/**
	 * @param $configCacheFileName
	 * @param bool $useBatchClient
	 * @return array
	 */
	public static function loadAndSaveKconfConfig($configCacheFileName, $useBatchClient = false)
	{
		if ($useBatchClient)
		{
			list($cloudStorage, $runtimeConfig) = self::loadConfFromApi();
		}
		else
		{
			list($cloudStorage, $runtimeConfig)  = self::loadConfFromKConf();
		}

		$s3Arn = isset($cloudStorage['s3Arn']) ? $cloudStorage['s3Arn'] : null;
		$storageOptions = isset($cloudStorage['storage_options']) ? $cloudStorage['storage_options'] : array();
		$storageTypeMap = isset($cloudStorage['storage_type_map']) ? $cloudStorage['storage_type_map'] : array();
		$remoteChunkConfigStaticFileCacheTime = isset($runtimeConfig['remote_chunk_config_static_file_cache_time']) ? $runtimeConfig['remote_chunk_config_static_file_cache_time'] : 120;
		$ffmpegReconnectParams = isset($runtimeConfig['ffmpeg_reconnect_params']) ? $runtimeConfig['ffmpeg_reconnect_params'] : null;

		$sharedStorageConfig = array(
			'arnRole' => $s3Arn,
			'storageTypeMap' => $storageTypeMap,
			'ffmpegReconnectParams' => $ffmpegReconnectParams,
			's3Region' => isset($storageOptions['s3Region']) ? $storageOptions['s3Region'] : null,
			'expirationTime' => time() + $remoteChunkConfigStaticFileCacheTime
		);

		$sharedStorageConfig['endPoint'] = isset($storageOptions['endPoint']) ? $storageOptions['endPoint'] : null;
		$sharedStorageConfig['accessKeyId'] = isset($storageOptions['accessKeyId']) ? $storageOptions['accessKeyId'] : null;
		$sharedStorageConfig['accessKeySecret'] = isset($storageOptions['accessKeySecret']) ? $storageOptions['accessKeySecret'] : null;
		$sharedStorageConfig['concurrency'] = isset($storageOptions['concurrency']) ? $storageOptions['concurrency'] : null;
		$sharedStorageConfig['maxConcurrentUploadConnections'] = isset($storageOptions['maxConcurrentUploadConnections']) ? $storageOptions['maxConcurrentUploadConnections'] : null;
		$sharedStorageConfig['userAgentRegex'] = isset($storageOptions['userAgentRegex']) ? $storageOptions['userAgentRegex'] : null;
		$sharedStorageConfig['userAgentPartner'] = isset($storageOptions['userAgentPartner']) ? $storageOptions['userAgentPartner'] : null;
		
		$sharedStorageConfig['region'] = isset($storageOptions['region']) ? $storageOptions['region'] : null;
		$sharedStorageConfig['namespaceName'] = isset($storageOptions['namespaceName']) ? $storageOptions['namespaceName'] : null;
		$sharedStorageConfig['configFileLocation'] = isset($storageOptions['configFileLocation']) ? $storageOptions['configFileLocation'] : null;

		KalturaLog::debug("Config loaded: " . print_r($sharedStorageConfig, true));
		kFile::safeFilePutContents($configCacheFileName, serialize($sharedStorageConfig));
		kCacheConfFactory::close();
		return $sharedStorageConfig;
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
		kSharedFileSystemMgr::setFileSystemOptions('arnRole', $storageRunParams['arnRole']);
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
		
		if(isset($storageRunParams['userAgentRegex'])) {
			kSharedFileSystemMgr::setFileSystemOptions('userAgentRegex', $storageRunParams['userAgentRegex']);
		}
		
		if(isset($storageRunParams['userAgentPartner'])) {
			kSharedFileSystemMgr::setFileSystemOptions('userAgentPartner', $storageRunParams['userAgentPartner']);
		}
		
		if(isset($storageRunParams['region'])) {
			kSharedFileSystemMgr::setFileSystemOptions('region', $storageRunParams['region']);
		}
		
		if(isset($storageRunParams['namespaceName'])) {
			kSharedFileSystemMgr::setFileSystemOptions('namespaceName', $storageRunParams['namespaceName']);
		}
		
		if(isset($storageRunParams['configFileLocation'])) {
			kSharedFileSystemMgr::setFileSystemOptions('configFileLocation', $storageRunParams['configFileLocation']);
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
		
		$ffmpegReconnectParams = self::getKconfParam('ffmpegReconnectParams');
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