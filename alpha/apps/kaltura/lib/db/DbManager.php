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
	 * @param int
	 */
	protected static $cachedConnIndex = false;
	
	/**
	 * @param int
	 */
	protected static $connIndex = false; 
	
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

	protected static function setSphinxConnIndexInCache()
	{
		if (!self::$sphinxCache || self::$connIndex === self::$cachedConnIndex)
			return;

		$stickySessionExpiry = isset(self::$config['sphinx_datasources']['sticky_session_timeout']) ? self::$config['sphinx_datasources']['sticky_session_timeout'] : 600;
		self::$sphinxCache->set(self::$stickySessionKey, self::$connIndex, $stickySessionExpiry);
		self::$cachedConnIndex = self::$connIndex;
	}

	protected static function getSphinxConnIndexFromCache()
	{
		self::$sphinxCache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_SPHINX_STICKY_SESSIONS);
		if (!self::$sphinxCache)
			return false;
		
		self::$stickySessionKey = self::getStickySessionKey();
		$preferredIndex = self::$sphinxCache->get(self::$stickySessionKey);
		if ($preferredIndex === false)
			return false;
		self::$cachedConnIndex = (int) $preferredIndex; //$preferredIndex returns from self::$sphinxCache->get(..) in type string
		return $preferredIndex;
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
	public static function getSphinxConnection($read = true)
	{
		if(!self::$sphinxConnection)
		{
			$sphinxDS = isset(self::$config['sphinx_datasources']['datasources']) ? self::$config['sphinx_datasources']['datasources'] : array(self::DB_CONFIG_SPHINX);
			$cacheExpiry = isset(self::$config['sphinx_datasources']['cache_expiry']) ? self::$config['sphinx_datasources']['cache_expiry'] : 30;
			$connectTimeout = isset(self::$config['sphinx_datasources']['connect_timeout']) ? self::$config['sphinx_datasources']['connect_timeout'] : 1;
			
			$preferredIndex = self::getSphinxConnIndexFromCache();

			list(self::$sphinxConnection, self::$connIndex) = self::connectFallbackLogic(
				array('DbManager', 'getSphinxConnectionInternal'), 
				array($connectTimeout), 
				$sphinxDS, 
				$preferredIndex, 
				$cacheExpiry);
			if (!self::$sphinxConnection)
			{
				throw new Exception('Failed to connect to any Sphinx config');
			}
		}
	
		if (!$read)
			self::setSphinxConnIndexInCache();
		return self::$sphinxConnection;
	}
	
	private static function getSphinxConnectionInternal($key, $connectTimeout)
	{
		if(!isset(self::$config['datasources'][$key]['connection']['dsn']))
			throw new Exception("DB Config [$key] not found");

		$dataSource = self::$config['datasources'][$key]['connection']['dsn'];
		self::$sphinxConnection = new KalturaPDO($dataSource, null, null, array(PDO::ATTR_TIMEOUT => $connectTimeout, KalturaPDO::KALTURA_ATTR_NAME => $key), $key);					
		self::$sphinxConnection->setCommentsEnabled(false);
		
		return self::$sphinxConnection;
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
