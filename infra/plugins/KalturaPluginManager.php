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
	
	protected function __construct()
	{
		
	}
	
	/**
	 * @param KalturaPluginManager::OBJECT_TYPE $objectType
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	public static function loadObject($objectType, $enumValue, array $constructorArgs = null)
	{
		$pluginInstances = self::getPluginInstances('IKalturaObjectLoaderPlugin');
		foreach($pluginInstances as $pluginName => $pluginInstance)
		{
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
		$pluginInstances = self::getPluginInstances('IKalturaObjectLoaderPlugin');
		foreach($pluginInstances as $pluginName => $pluginInstance)
		{
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
	 * @return array<KalturaPlugin>
	 */
	public static function getPluginInstances($interface = null)
	{
		if(!count(self::$pluginInstances))
		{
			self::$pluginInstances = array();
			$plugins = self::getPlugins();
			
			foreach($plugins as $pluginName => $pluginClass)
				if($pluginClass && class_exists($pluginClass))
					self::$pluginInstances[$pluginName] = new $pluginClass();
		}
		
		if(is_null($interface))
			return self::$pluginInstances;
		
		$instances = array();
		foreach(self::$pluginInstances as $pluginInstance)
		{
			$moreInstances = $pluginInstance->getInstances($interface);
			foreach($moreInstances as $instance)
				$instances[strtolower($instance->getPluginName())] = $instance;
		}
		return $instances;
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
}
