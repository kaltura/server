<?php
// TODO - remove after config moved to DB or external file
require_once realpath(dirname(__FILE__) . '/../../') . '/alpha/config/kConf.php';


class KalturaPluginManager
{
	const OBJECT_TYPE_SYNCABLE = 2;
	const OBJECT_TYPE_MEDIA_SOURCE = 3;
	const OBJECT_TYPE_OPERATION_ENGINE = 4;
	const OBJECT_TYPE_KDL_ENGINE = 5;
	
	const OBJECT_TYPE_ENTRY = 101;
	const OBJECT_TYPE_FLAVOR_PARAMS = 102;
	const OBJECT_TYPE_FLAVOR_PARAMS_OUTPUT = 103;

	const OBJECT_TYPE_KALTURA_ENTRY = 201;
	const OBJECT_TYPE_KALTURA_FLAVOR_PARAMS = 202;
	const OBJECT_TYPE_KALTURA_FLAVOR_PARAMS_OUTPUT = 203;
		
	/**
	 * @var array<string, string> in the form array[pluginName] = pluginClass
	 */
	protected static $plugins = array();
	
	/**
	 * @var array<KalturaPlugin>
	 */
	protected static $pluginInstances = array();
	
	/**
	 * @var array<KalturaPluginFileSyncObjectManager>
	 */
	protected static $fileSyncObjectManagers = array();
	
	/**
	 * @var array<string> service config path
	 */
	protected static $serviceConfigs = array();
	
	/**
	 * @var array<array> db config
	 */
	protected static $dbConfigs = array();
	
	
	protected function __construct()
	{
		
	}
	
	/**
	 * @param string $serviceId
	 * @return bool
	 */
	public static function isServiceExists($serviceId)
	{
		$class = self::getApiServiceClass($serviceId);
		return !is_null($class);
	}
	
	/**
	 * @param string $serviceId
	 * @return string service class
	 */
	public static function getApiServiceClass($serviceId)
	{
		if(strpos($serviceId, '_') <= 0)
			return false;

		$serviceId = strtolower($serviceId);
		list($servicePlugin, $serviceName) = explode('_', $serviceId);
		
		$pluginInstances = self::getPluginInstances();
		if(!isset($pluginInstances[$servicePlugin]))
			return null;
			
		$pluginInstance = $pluginInstances[$servicePlugin];
		if(!($pluginInstance instanceof KalturaServicesPlugin))
			return null;
			
		$servicesMap = $pluginInstance->getServicesMap();
		if(isset($servicesMap[$serviceName]))
			return $servicesMap[$serviceName];
			
		return null;
	}
	
	/**
	 * @return array<string, string> in the form array[serviceName] = serviceClass
	 */
	public static function getApiServices()
	{
		$services = array();
		$pluginInstances = self::getPluginInstances();
		foreach($pluginInstances as $pluginName => $pluginInstance)
		{
			if(!($pluginInstance instanceof KalturaServicesPlugin))
				continue;

//			KalturaLog::debug("Checking plugin [$pluginName] for API services");
			
			$pluginServices = $pluginInstance->getServicesMap();
			foreach($pluginServices as $serviceName => $serviceClass)
			{
				$serviceName = strtolower($serviceName);
				$services["{$pluginName}_{$serviceName}"] = $serviceClass;
			}
		}
		
		return $services;
	}
	
	
	/**
	 * @return array<string> service config path
	 */
	public static function getServiceConfigs()
	{
		if(count(self::$serviceConfigs))
			return self::$serviceConfigs;
			
		self::$serviceConfigs = array();
		$pluginInstances = self::getPluginInstances();
		foreach($pluginInstances as $pluginName => $pluginInstance)
		{
			if(!($pluginInstance instanceof KalturaServicesPlugin))
				continue;
				
//			KalturaLog::debug("Checking plugin [$pluginName] for service config files");
			$serviceConfig = $pluginInstance->getServiceConfig();
			if($serviceConfig)
				self::$serviceConfigs[$pluginName] = $serviceConfig;
		}
		
		return self::$serviceConfigs;
	}
	
	
	/**
	 * @return array<string> service config path
	 */
	public static function getDbConfigs()
	{
		if(count(self::$dbConfigs))
			return self::$dbConfigs;
			
		self::$dbConfigs = array();
		$pluginInstances = self::getPluginInstances();
		foreach($pluginInstances as $pluginName => $pluginInstance)
		{
			if(!($pluginInstance instanceof KalturaDatabaseConfigPlugin))
				continue;
				
//			KalturaLog::debug("Checking plugin [$pluginName] for DB configurations");
			$dbConfig = $pluginInstance->getDatabaseConfig();
			if($dbConfig && is_array($dbConfig))
				self::$dbConfigs[$pluginName] = $dbConfig;
		}
		
		return self::$dbConfigs;
	}
	
	/**
	 * @param KalturaPluginManager::OBJECT_TYPE $objectType
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	public static function loadObject($objectType, $enumValue, array $constructorArgs = null)
	{
		$pluginInstances = self::getPluginInstances();
		foreach($pluginInstances as $pluginName => $pluginInstance)
		{
			if(!($pluginInstance instanceof KalturaObjectLoaderPlugin))
				continue;
				
//			KalturaLog::debug("Checking plugin [$pluginName] for object loaders");
			$obj = $pluginInstance->loadObject($objectType, $enumValue, $constructorArgs);
			if($obj)
				return $obj;
		}
		
		return null;
	}
	
	/**
	 * @param KalturaPluginManager::OBJECT_TYPE $objectType
	 * @param string $enumValue
	 * @return object
	 */
	public static function getObjectClass($objectType, $enumValue)
	{
		$pluginInstances = self::getPluginInstances();
		foreach($pluginInstances as $pluginName => $pluginInstance)
		{
			if(!($pluginInstance instanceof KalturaObjectLoaderPlugin))
				continue;
		
//			KalturaLog::debug("Checking plugin [$pluginName] for class getters");
			$cls = $pluginInstance->getObjectClass($objectType, $enumValue);
			if($cls)
			{
//				KalturaLog::debug("Found class[$cls] in plugin[$pluginName] for object type[$objectType] and enum value[$enumValue]");
				return $cls;
			}
		}
		
		return null;
	}
	
	/**
	 * @return array
	 */
	public static function getEventConsumers()
	{
		$eventConsumers = array();
		$pluginInstances = self::getPluginInstances();
		foreach($pluginInstances as $pluginName => $pluginInstance)
		{
			if(!($pluginInstance instanceof KalturaEventConsumersPlugin))
				continue;
		
//			KalturaLog::debug("Checking plugin [$pluginName] for event consumers");
			$pluginEventConsumers = $pluginInstance->getEventConsumers();
			if(!$pluginEventConsumers || !count($pluginEventConsumers))
				continue;
			
			foreach($pluginEventConsumers as $pluginEventConsumer)
				$eventConsumers[] = $pluginEventConsumer;
		}
		
		return $eventConsumers;
	}
	
	/**
	 * @return array<KalturaAdminConsolePlugin>
	 */
	public static function getAdminConsolePages()
	{
		$adminConsolePages = array();
		$pluginInstances = self::getPluginInstances();
		foreach($pluginInstances as $pluginName => $pluginInstance)
		{
			if(!($pluginInstance instanceof KalturaAdminConsolePagesPlugin))
				continue;
		
//			KalturaLog::debug("Checking plugin [$pluginName] for admin console pages");
			$pluginAdminConsolePages = $pluginInstance->getAdminConsolePages();
			if(!$pluginAdminConsolePages || !count($pluginAdminConsolePages))
				continue;
			
			foreach($pluginAdminConsolePages as $pluginAdminConsolePage)
				$adminConsolePages[] = $pluginAdminConsolePage;
		}
		
		return $adminConsolePages;
	}
	
	/**
	 * @return array<KalturaPlugin>
	 */
	public static function getPluginInstances()
	{
		if(count(self::$pluginInstances))
			return self::$pluginInstances;

		self::$pluginInstances = array();
		$plugins = self::getPlugins();
		
		foreach($plugins as $pluginName => $pluginClass)
			if($pluginClass && class_exists($pluginClass))
				self::$pluginInstances[$pluginName] = new $pluginClass();
				
		return self::$pluginInstances;
	}
	
	/**
	 * @param string pluginName
	 * @return KalturaPlugin
	 */
	public static function getPluginInstance($pluginName)
	{
		//TODO - do we need to get all the instances? maybe create just the required plugin.
		// unless they are all created at bootstrap anyway for event handling purposes
		$plugins = self::getPluginInstances();
		return @$plugins[str_replace('plugin', '', strtolower($pluginName))];
	}
	
	/**
	 * @return array<string, string> in the form array[pluginName] = pluginClass
	 */
	public static function getPlugins()
	{
		if(count(self::$plugins))
			return self::$plugins;
			
		if(!kConf::hasParam("default_plugins"))
			return array();
			
		$plugins = kConf::get("default_plugins");
		if(!is_array($plugins))
			return array();
			
		self::$plugins = array();
		foreach($plugins as $pluginClass)	
		{
			$pluginName = str_replace('plugin', '', strtolower($pluginClass));
			self::$plugins[$pluginName] = $pluginClass;
		}
			
		return self::$plugins;
	}

	/**
	 * @param string $entryId the new created entry
	 * @param array $data key => value pairs
	 */
	public static function handleBulkUploadData($entryId, array $data)
	{
		$bulkUploadColumns = array();
		$pluginInstances = self::getPluginInstances();
		foreach($pluginInstances as $pluginName => $pluginInstance)
		{
			if(!($pluginInstance instanceof KalturaBulkUploadHandlerPlugin))
				continue;
		
//			KalturaLog::debug("Checking plugin [$pluginName] for bulk upload handlers");
			$pluginInstance->handleBulkUploadData($entryId, $data);
		}
	}
}
