<?php
/**
 *  @package infra
 *  @subpackage DB
 */
class DbManager 
{
	const DB_CONFIG_SPHINX = 'sphinx';
	
	/**
	 * @var array
	 */
	protected static $config = array();
	
	/**
	 * @var array
	 */
	protected static $sphinxConnection = null;
	
	public static function setConfig(array $config)
	{
		self::$config = $config;
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
	
	public static function initialize() 
	{
		$dbConfigs = array();
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaDatabaseConfig');
		foreach($pluginInstances as $pluginInstance)
			$dbConfigs[] = $pluginInstance->getDatabaseConfig();
		
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
			return new KalturaPDO($dsn);
		}
		catch(PropelException $pex)
		{
			KalturaLog::alert($pex->getMessage());
			throw new PropelException("Database error");
		}
	}

	/**
	 * @return KalturaPDO
	 */
	public static function getSphinxConnection()
	{
		if(self::$sphinxConnection)
			return self::$sphinxConnection;

		$sphinxDS = isset(self::$config['sphinx_datasources']['datasources']) ? self::$config['sphinx_datasources']['datasources'] : array(self::DB_CONFIG_SPHINX);
		$cacheExpiry = isset(self::$config['sphinx_datasources']['cache_expiry']) ? self::$config['sphinx_datasources']['cache_expiry'] : 300;

		// loop twice, on first iteration try only connections not marked as failed
		// in case all connections failed, try all connections on second iteration

		$iteration = 2;
		while($iteration--)
		{
			$count = count($sphinxDS);
			$offset = mt_rand(0, $count - 1);


			while($count--)
			{
				$key = $sphinxDS[($count + $offset) % count($sphinxDS)];

				$cacheKey = "sphinxCon:".$key;

				if (function_exists('apc_fetch'))
				{
					if (!$iteration) // on second iteration reset failed connection flag
						apc_store($cacheKey, 0);
					else if (apc_fetch($cacheKey)) // if connection failed to connect in the past mark it
						continue;
				}

				try {
					if(!isset(self::$config['datasources'][$key]['connection']['dsn']))
						throw new Exception("DB Config [$key] not found");

					$dataSource = self::$config['datasources'][$key]['connection']['dsn'];
					self::$sphinxConnection = new KalturaPDO($dataSource);

					KalturaLog::debug("getSphinxConnection: connected to $key");
					return self::$sphinxConnection;
				}
				catch(PropelException $pex)
				{
					KalturaLog::alert($pex->getMessage());
					throw new PropelException("Database error");
				}
				catch(Exception $ex)
				{
					KalturaLog::debug("getSphinxConnection: failed to connect to $key");
					if (function_exists('apc_store'))
						apc_store($cacheKey, 1, $cacheExpiry);
				}
			}
		}

		KalturaLog::debug("getSphinxConnection: Failed to connect to any Sphinx config");
		throw new Exception('Failed to connect to any Sphinx config');
	}
}
