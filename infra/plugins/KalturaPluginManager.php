<?php
/**
 * @package infra
 * @subpackage Plugins
 * @todo remove kConf require after config moved to DB or external file
 */
require_once realpath(dirname(__FILE__) . '/../../') . '/infra/kConf.php';

/**
 * @package infra
 * @subpackage Plugins
 */
class KalturaPluginManager
{
	/**
	 * Array of all installed plugin classes
	 * @var array<string, string> in the form array[pluginName] = pluginClass
	 */
	protected static $plugins = array();
	
	/**
	 * Array of all installed plugin instantiated classes
	 * @var array<KalturaPlugin>
	 */
	protected static $pluginInstances = array();
	
	protected function __construct()
	{
		
	}
	
	/**
	 * Loads an extended object that extended by plugin
	 * @param string $baseClass
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		$pluginInstances = self::getPluginInstances('IKalturaObjectLoader');
		foreach($pluginInstances as $pluginName => $pluginInstance)
		{
			$obj = $pluginInstance->loadObject($baseClass, $enumValue, $constructorArgs);
			if($obj)
				return $obj;
		}
		
		return null;
	}
	
	/**
	 * Return all enum values that extend the base enum value
	 * @param string $baseClass
	 * @param string $enumValue
	 * @return array
	 */
	public static function getExtendedTypes($baseClass, $enumValue)
	{
		$values = array($enumValue);
		$pluginInstances = self::getPluginInstances('IKalturaTypeExtender');
		foreach($pluginInstances as $pluginName => $pluginInstance)
		{
			$pluginValues = $pluginInstance->getExtendedTypes($baseClass, $enumValue);
			if($pluginValues && count($pluginValues))
				foreach($pluginValues as $pluginValue)
					$values[] = $pluginValue;
		}
		
		return $values;
	}
	
	/**
	 * @param Iterator $srcConfig
	 * @param Iterator $newConfig
	 * @param bool $valuesOnly
	 * @return Iterator
	 */
	protected static function mergeConfigItem(Iterator $srcConfig, Iterator $newConfig, $valuesOnly)
	{
		$returnedConfig = $srcConfig;
		
		if($valuesOnly)
		{
			foreach($srcConfig as $key => $value)
			{
				if(!$newConfig->$key) // nothing to append
					continue;
				elseif($value instanceof Iterator)
					$returnedConfig->$key = self::mergeConfigItem($srcConfig->$key, $newConfig->$key, $valuesOnly);
				else
					$returnedConfig->$key = $srcConfig->$key . ',' . $newConfig->$key;
			}
		}
		else
		{
			foreach($newConfig as $key => $value)
			{
				if(!$srcConfig->$key)
					$returnedConfig->$key = $newConfig->$key;
				elseif($value instanceof Iterator)
					$returnedConfig->$key = self::mergeConfigItem($srcConfig->$key, $newConfig->$key, $valuesOnly);
				else
					$returnedConfig->$key = $srcConfig->$key . ',' . $newConfig->$key;
			}
		}
		
		return $returnedConfig;
	}
	
	/**
	 * Merge configuration data from the plugins
	 * 
	 * @param Iterator $config the configuration to be merged
	 * @param string $configName
	 * @param bool $valuesOnly if true, new keys won't be added to the original config
	 * @return Iterator
	 */
	public static function mergeConfigs(Iterator $config, $configName, $valuesOnly = true)
	{
		$pluginInstances = self::getPluginInstances('IKalturaConfigurator');
		foreach($pluginInstances as $pluginName => $pluginInstance)
		{
			$pluginConfig = $pluginInstance->getConfig($configName);
			if($pluginConfig)
				$config = self::mergeConfigItem($config, $pluginConfig, $valuesOnly);
		}
		
		return $config;
	}
	
	/**
	 * Finds extended class the extended by plugin
	 * @param string $baseClass
	 * @param string $enumValue
	 * @return object
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		$pluginInstances = self::getPluginInstances('IKalturaObjectLoader');
		foreach($pluginInstances as $pluginName => $pluginInstance)
		{
			$cls = $pluginInstance->getObjectClass($baseClass, $enumValue);
			if($cls)
			{
//				KalturaLog::debug("Found class[$cls] in plugin[$pluginName] for object type[$objectType] and enum value[$enumValue]");
				return $cls;
			}
		}
		
		return null;
	}
	
	/**
	 * Validates plugin according to its dependencies
	 * @param string $pluginClass 
	 * @param array $validatedPlugins list of plugins that already validated
	 * @return bool false if a required dependency is missing.
	 */
	protected static function isValid($pluginClass, array $validatedPlugins = null)
	{
		$pluginClassReplection = new ReflectionClass($pluginClass);
		if(!$pluginClassReplection->implementsInterface('IKalturaPending'))
			return true;
			
		// TODO remove call_user_func after moving to php 5.3
		$pendingPlugins = call_user_func(array($pluginClass, 'dependsOn'));
//		$pendingPlugins = $pluginClass::dependsOn();
		if(!$pendingPlugins || !count($pendingPlugins))
			return true;
			
		$availablePlugins = self::getPlugins();
		foreach($pendingPlugins as $pendingPlugin)
		{
			$pendingPluginName = $pendingPlugin->getPluginName();
			
			// check if the required plugin is configured to be loaded
			if(!isset($availablePlugins[$pendingPluginName]))
			{
				KalturaLog::err("Pending plugin name [$pendingPluginName] is not available, plugin [$pluginClass] could not be loaded.");
				return false;
			}
				
			$pendingPluginClass = $availablePlugins[$pendingPluginName];
			$pendingPluginReplection = new ReflectionClass($pendingPluginClass);
				
			// check if the required plugin already validated
			if(in_array($pendingPluginName, $validatedPlugins))
				continue;
			
			// check if the version compatible
			$pendingPluginMinVersion = $pendingPlugin->getMinimumVersion();
			if($pendingPluginMinVersion && $pendingPluginReplection->implementsInterface('IKalturaVersion'))
			{
				// TODO remove call_user_func after moving to php 5.3
				$pendingPluginVersion = call_user_func(array($pendingPluginClass, 'getVersion'));
//				$pendingPluginVersion = $pendingPluginClass::getVersion();
				if(!$pendingPluginVersion->isCompatible($pendingPluginMinVersion))
				{
					KalturaLog::err("Pending plugin name [$pendingPluginName] version [$pendingPluginVersion] is not compatible with required version [$pendingPluginMinVersion], plugin [$pluginClass] could not be loaded.");
					return false;
				}
			}
			
			// adds tested plugin name to the list of validated in order to avoid endless recursion
			$tempValidatedPlugins = $validatedPlugins;
			// TODO remove call_user_func after moving to php 5.3
			$tempValidatedPlugins[] = call_user_func(array($pluginClass, 'getPluginName'));
//			$tempValidatedPlugins[] = $pluginClass::getPluginName();
			if(!self::isValid($pendingPluginClass, $tempValidatedPlugins))
			{
				KalturaLog::err("Plugin [$pluginClass] could not be loaded.");
				return false;
			}
				
			// adds the last tested dependency plugin to the valid list
			$validatedPlugins[] = $pendingPluginName;
		}
		
		return true;
	}
	
	/**
	 * Returns all instances that implement the requested interface or all of them in not supplied
	 * @param string $interface
	 * @return array<KalturaPlugin>
	 */
	public static function getPluginInstances($interface = null)
	{
		if(!count(self::$pluginInstances))
		{
			self::$pluginInstances = array();
			$plugins = self::getPlugins();
			
			foreach($plugins as $pluginName => $pluginClass)
			{
				if(!$pluginClass || !class_exists($pluginClass))
					continue;

				if(!self::isValid($pluginClass, array_keys(self::$pluginInstances)))
					continue;
					
				self::$pluginInstances[$pluginName] = new $pluginClass();
			}
		}
		
		if(is_null($interface))
			return self::$pluginInstances;
		
		$instances = array();
		foreach(self::$pluginInstances as $pluginInstance)
		{
			if ($pluginInstance instanceof IKalturaPlugin)
			{
				$instance = $pluginInstance->getInstance($interface);
				if ($instance)
					$instances[strtolower($pluginInstance->getPluginName())] = $instance;
			}
		}
		return $instances;
	}
	
	/**
	 * Returns a single plugin instance by its name
	 * @param string pluginName
	 * @return KalturaPlugin
	 */
	public static function getPluginInstance($pluginName)
	{
		//TODO - do we need to get all the instances? maybe create just the required plugin.
		// unless they are all created at bootstrap anyway for event handling purposes
		$plugins = self::getPluginInstances();
		return @$plugins[$pluginName];
	}
	
	/**
	 * @param string $pluginClass
	 */
	public static function addPlugin($pluginClass)
	{
		self::getPluginInstances();
		$plugin = new $pluginClass();
		$pluginName = $plugin->getPluginName();
		self::$plugins[$pluginName] = $pluginClass;
		self::$pluginInstances[$pluginName] = $plugin;
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
			if(!class_exists($pluginClass))
				continue;
				
			// TODO remove call_user_func after moving to php 5.3
			$pluginName = call_user_func(array($pluginClass, 'getPluginName'));
//			$pluginName = $pluginClass::getPluginName();
			self::$plugins[$pluginName] = $pluginClass;
		}
			
		return self::$plugins;
	}
}
