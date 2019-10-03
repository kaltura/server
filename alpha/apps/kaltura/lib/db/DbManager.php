<?php
/**
 *  @package server-infra
 *  @subpackage DB
 */
class DbManager 
{
	const DB_CONFIG_SPHINX = 'sphinx';
	
	const EXTRA_DB_CONFIG_KEY = 'extra_db_configs';
	
	const STICKY_SESSION_PREFIX = 'StickySessionIndex:';
	
	/**
	 * @var array
	 */
	protected static $config = array();
	
	/**
	 * @var array
	 */
	protected static $kalturaConfig = array();
	
	/**
	 * @var array
	 */
	protected static $sphinxConnection = null;
	
	/**
	 * @var kBaseCacheWrapper 
	 */
	protected static $sphinxCache = null; 
	
	/**
	 * @param string 
	 */
	protected static  $stickySessionKey = null;
	
	/**
	 * @param array
	 */
	protected static $cachedConnIndexes = false;
	
	/**
	 * @param array
	 */
	protected static $connIndexes = false;

	public static function setConfig(array $config)
	{
		$reflect = new ReflectionClass('KalturaPDO');
		$optionAttributes = $reflect->getConstants();
		
		foreach ($config['datasources'] as $connectionName => & $connectionConfig) 
		{
			if(!is_array($connectionConfig) || !isset($connectionConfig['connection']))
				continue;
				
			if(!isset($connectionConfig['connection']['options']))
				$connectionConfig['connection']['options'] = array();
			$connectionOptions = & $connectionConfig['connection']['options'];
			$connectionOptions['KalturaPDO::KALTURA_ATTR_NAME'] = array('value' => $connectionName);
		
			if(isset($connectionOptions['kaltura']))
			{
				self::$kalturaConfig[$connectionName] = $connectionOptions['kaltura'];
				unset($connectionOptions['kaltura']);
			}
		}
		
		self::$config = $config;
	}
	
	public static function getKalturaConfig($connectionName)
	{
		if(isset(self::$kalturaConfig[$connectionName]))
			return self::$kalturaConfig[$connectionName];
			
		return array();
	}
	
	public static function getConfig($config)
	{
		return self::$config;
	}
	
	public static function addExtraConfiguration(array $config)
	{
		self::$config = array_merge_recursive(self::$config, $config);
		Propel::setConfiguration(self::$config);
	}
	
	protected static function getExtraDatabaseConfigs()
	{
		if (function_exists('apc_fetch'))
		{
			$dbConfigs = apc_fetch(self::EXTRA_DB_CONFIG_KEY);
			if ($dbConfigs !== false)
			{
				return $dbConfigs;
			}
		}
			
		$dbConfigs = array();
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaDatabaseConfig');
		foreach($pluginInstances as $pluginInstance)
		{
			/* @var $pluginInstance IKalturaDatabaseConfig */
			$dbConfigs[] = $pluginInstance->getDatabaseConfig();
		}

		if (function_exists('apc_store'))
		{
			apc_store(self::EXTRA_DB_CONFIG_KEY, $dbConfigs);
		}
		
		return $dbConfigs;
	}
	
	public static function initialize() 
	{
		$dbConfigs = self::getExtraDatabaseConfigs();	
		foreach($dbConfigs as $dbConfig)
			self::addExtraConfiguration($dbConfig);
		
		Propel::setConfiguration(self::$config);
		Propel::setLogger(KalturaLog::getInstance());
		
		try
		{
			Propel::initialize();
		}
		catch(PropelException $pex)
		{
			KalturaLog::alert($pex->getMessage());
			throw new PropelException("Database error");
		}
	}
	
	public static function shutdown()
	{
		Propel::close();
	}
	
	/**
	 * @return KalturaPDO
	 */
	public static function createSphinxConnection($sphinxServer, $port = 9312)
	{
		$dsn = "mysql:host=$sphinxServer;port=$port;";
		
		try
		{
			$con = new KalturaPDO($dsn);
			$con->setCommentsEnabled(false);
			return $con;
		}
		catch(PropelException $pex)
		{
			KalturaLog::alert($pex->getMessage());
			throw new PropelException("Database error");
		}
	}

	protected static function setSphinxConnIndexInCache($indexName = null)
	{
		if (!self::$sphinxCache ||
			(isset(self::$connIndexes[$indexName]) && isset(self::$cachedConnIndexes[$indexName]) &&
				self::$connIndexes[$indexName] == self::$cachedConnIndexes[$indexName])
		)
		{
			return;
		}

		$stickySessionExpiry = isset(self::$config['sphinx_datasources']['sticky_session_timeout']) ? self::$config['sphinx_datasources']['sticky_session_timeout'] : 600;
		KalturaLog::debug("Setting sphinx sticky session for key [" . self::$stickySessionKey . "] to sphinx index [" . print_r(self::$connIndexes, true) . "]");
		self::$sphinxCache->set(self::$stickySessionKey, self::$connIndexes , $stickySessionExpiry);
		self::$cachedConnIndexes[$indexName] = self::$connIndexes[$indexName];
	}

	protected static function getSphinxConnIndexFromCache($indexName = null)
	{
		self::$sphinxCache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_SPHINX_STICKY_SESSIONS);
		if (!self::$sphinxCache)
		{
			return false;
		}
		
		self::$stickySessionKey = self::getStickySessionKey();
		$preferredIndex = self::$sphinxCache->get(self::$stickySessionKey);
		KalturaLog::debug("Got sphinx sticky session for key [" . self::$stickySessionKey . "] to sphinx index [" . print_r($preferredIndex, true) . "]");
		
		if ($preferredIndex === false || !isset($preferredIndex[$indexName]))
		{
			return false;
		}
			
		self::$cachedConnIndexes[$indexName] = (int)$preferredIndex[$indexName]; //$preferredIndex returns from self::$sphinxCache->get(..) in type string
		return $preferredIndex[$indexName];
	}

	/**
	 * choose the sphinx db with the smallest lag
	 * @param $dataSources
	 * @return bool|mixed
	 */
	protected static function getSphinxConnIndexByLastUpdatedAt($dataSources)
	{
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_QUERY_CACHE_KEYS);
		if (!$cache)
		{
			KalturaLog::debug("could not retrieve query cache keys form cache, no sphinx index will be chosen by updatedAt");
			return false;
		}

		$cacheResult = $cache->get(kQueryCache::SPHINX_LAG_KEY);
		if (!$cacheResult)
		{
			KalturaLog::debug("failed to get sphinx_lag_key from memcache, no sphinx index will be chosen by updatedAt");
			return false;
		}

		$lastUpdatedAtPerSphinx = json_decode($cacheResult, true);
		if (empty($lastUpdatedAtPerSphinx))
		{
			KalturaLog::debug("failed decoding sphinx last updated ids, no sphinx index will be chosen by updatedAt");
			return false;
		}

		list($hostToLag, $hostToIndex) = self::filterLagsAndHosts($dataSources, $lastUpdatedAtPerSphinx);
		return self::getPreferredSphinxIndexByWeight($hostToLag, $hostToIndex);
	}

	protected static function getStickySessionKey()
	{
		$ksObject = kCurrentContext::$ks_object;

		if($ksObject && $ksObject->hasPrivilege(kSessionBase::PRIVILEGE_SESSION_KEY)) 
			return self::STICKY_SESSION_PREFIX . kCurrentContext::getCurrentPartnerId() . "_" . $ksObject->getPrivilegeValue(kSessionBase::PRIVILEGE_SESSION_KEY);
		
		return self::STICKY_SESSION_PREFIX . infraRequestUtils::getRemoteAddress();
	}
	
	/**
	 * @return KalturaPDO
	 */
	public static function getSphinxConnection($read = true, $indexName = null)
	{
		KalturaLog::debug("Using index with name [$indexName]");
		if(!isset(self::$sphinxConnection[$indexName]))
		{
			if($indexName && isset(self::$config['sphinx_datasources_'.$indexName]['datasources']))
			{
				$sphinxDS = self::$config['sphinx_datasources_'.$indexName]['datasources'];
			}
			else
			{
				$sphinxDS = isset(self::$config['sphinx_datasources']['datasources']) ? self::$config['sphinx_datasources']['datasources'] : array(self::DB_CONFIG_SPHINX);
			}
			
			$dedicatedDataSourceAccounts = kConf::getMap('dedicated_datasource_accounts');
			$currentPartnerId = kCurrentContext::getCurrentPartnerId();
			if($currentPartnerId && in_array($currentPartnerId, $dedicatedDataSourceAccounts) && isset(self::$config['sphinx_datasources']['dedicated_datasources']))
			{
				$sphinxDS = self::$config['sphinx_datasources']['dedicated_datasources'];
			}

			$cacheExpiry = isset(self::$config['sphinx_datasources']['cache_expiry']) ? self::$config['sphinx_datasources']['cache_expiry'] : 30;
			$connectTimeout = isset(self::$config['sphinx_datasources']['connect_timeout']) ? self::$config['sphinx_datasources']['connect_timeout'] : 1;
			
			$preferredIndex = self::getSphinxConnIndexFromCache($indexName);
			if ($preferredIndex === false)
				$preferredIndex = self::getSphinxConnIndexByLastUpdatedAt($sphinxDS);

			list(self::$sphinxConnection[$indexName], self::$connIndexes[$indexName]) = self::connectFallbackLogic(
				array('DbManager', 'getSphinxConnectionInternal'), 
				array($connectTimeout, $indexName),
				$sphinxDS, 
				$preferredIndex, 
				$cacheExpiry);
			if (!self::$sphinxConnection[$indexName])
			{
				throw new Exception('Failed to connect to any Sphinx config');
			}
			KalturaLog::debug("Actual sphinx index [". self::$connIndexes[$indexName]. "] sphinx index by best lag [" . $preferredIndex. "]");
		}
	
		if (!$read)
			self::setSphinxConnIndexInCache($indexName);
		return self::$sphinxConnection[$indexName];
	}

	/**
	 * @param $dataSources
	 * @param $lastUpdatedAtPerSphinx
	 * @return array
	 */
	protected static function filterLagsAndHosts($dataSources, $lastUpdatedAtPerSphinx)
	{
		$hostToLag = array();
		$now = time();
		$hostToIndex = array();

		foreach ($dataSources as $key => $datasource)
		{
			if (!isset(self::$config['datasources'][$datasource]['connection']['dsn']))
				continue;

			preg_match('/host=(.*?);/', self::$config['datasources'][$datasource]['connection']['dsn'], $matches);
			if (!$matches || !$matches[1])
				continue;

			$currentHost = $matches[1];
			if (array_key_exists($currentHost, $lastUpdatedAtPerSphinx) && is_numeric($lastUpdatedAtPerSphinx[$currentHost]))
			{
				$hostToLag[$currentHost] = max($now - $lastUpdatedAtPerSphinx[$currentHost],0);
				$hostToIndex[$currentHost] = $key;
			}
		}
		return array($hostToLag, $hostToIndex);
	}

	/**
	 * @param $hostToLag
	 * @param $hostToIndex
	 * @return bool
	 */
	protected static function getPreferredSphinxIndexByWeight($hostToLag, $hostToIndex)
	{
		$maxLag = max(array_values($hostToLag));

		$baseRatio = 20;
		$weights = array();

		// calculate weight for each sphinx last updated id
		foreach ($hostToIndex as $currentHost => $key)
		{
			$weight = intval($baseRatio + ($maxLag - max($hostToLag[$currentHost], 0)) / ($maxLag + 1) * 100);
			$weights[$currentHost] = $weight;
		}

		$preferredWeight = rand(0, array_sum($weights));
		foreach ($weights as $currentHost => $weight)
		{
			$preferredWeight -= $weight;
			if ($preferredWeight <= 0)
			{
				KalturaLog::log("Chosen Sphinx [$currentHost]. Sphinx weights " . print_r($weights, true));
				return $hostToIndex[$currentHost];
			}
		}

		KalturaLog::debug("no sphinx was chosen by best last updated id");
		return false;
	}

	private static function getSphinxConnectionInternal($key, $connectTimeout, $indexName)
	{
		if(!isset(self::$config['datasources'][$key]['connection']['dsn']))
			throw new Exception("DB Config [$key] not found");

		$dataSource = self::$config['datasources'][$key]['connection']['dsn'];
		self::$sphinxConnection[$indexName] =
			new KalturaPDO($dataSource, null, null, array(PDO::ATTR_TIMEOUT => $connectTimeout, KalturaPDO::KALTURA_ATTR_NAME => $key), $key);
		self::$sphinxConnection[$indexName]->setCommentsEnabled(false);
		
		return self::$sphinxConnection[$indexName];
	}

	public static function connectFallbackLogic($connectCallback, array $connectParams, $dataSources, $preferredIndex = false, $cacheExpiry = 30)
	{
		// loop twice, on first iteration try only connections not marked as failed
		// in case all connections failed, try all connections on second iteration
		$iteration = 2;
		while($iteration--)
		{
			$count = count($dataSources);
			if ($preferredIndex !== false)
				$offset = $preferredIndex;
			else
				$offset = mt_rand(0, $count - 1);

			while($count--)
			{
				$curIndex = $offset % count($dataSources);
				$offset++;
				$key = $dataSources[$curIndex];

				if (function_exists('apc_fetch'))
				{
					$badConnCacheKey = "badDBConn:".$key;
					if (!$iteration) // on the second iteration reset failed connection flag
						apc_store($badConnCacheKey, false);
					else if (apc_fetch($badConnCacheKey)) // if connection failed to connect in the past mark it
						continue;
				}

				$params = array_merge(array($key), $connectParams);
				
				try 
				{
					$connection = call_user_func_array($connectCallback, $params);
					KalturaLog::debug("connected to $key");
					return array($connection, $curIndex);
				}
				catch(Exception $ex)
				{
					KalturaLog::err("failed to connect to $key");
				}

				if (function_exists('apc_store'))
				{
					apc_store($badConnCacheKey, true, $cacheExpiry);
				}
			}
		}
		return array(null, false);
	}
}
