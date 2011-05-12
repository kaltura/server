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
	protected static $sphinxConection = null;
	
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
		Propel::initialize();
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
		return new KalturaPDO($dsn);
	}
	
	/**
	 * @return KalturaPDO
	 */
	public static function getSphinxConnection()
	{
		if(self::$sphinxConection)
			return self::$sphinxConection;
			
		if(!isset(self::$config['datasources'][self::DB_CONFIG_SPHINX]['connection']['dsn']))
			throw new Exception('DB Config [' . self::DB_CONFIG_SPHINX . '] not found');
			
		self::$sphinxConection = new KalturaPDO(self::$config['datasources'][self::DB_CONFIG_SPHINX]['connection']['dsn']);
		return self::$sphinxConection;
	}
}
?>