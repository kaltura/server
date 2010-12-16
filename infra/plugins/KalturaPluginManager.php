<?php
// TODO - remove after config moved to DB or external file
require_once realpath(dirname(__FILE__) . '/../../') . '/alpha/config/kConf.php';


class KalturaPluginManager
{
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
		$pendingPlugins = call_user_func("$pluginClass::dependsOn");
//		$pendingPlugins = $pluginClass::dependsOn();
		if(!$pendingPlugins || !count($pendingPlugins))
			return true;
			
		$availablePlugins = self::getPlugins();
		foreach($pendingPlugins as $pendingPlugin)
		{
			$pendingPluginName = $pendingPlugin->getPluginName();
			
			// check if the required plugin is configured to be loaded
			if(!isset($availablePlugins[$pendingPluginName]))
				return false;
				
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
				$pendingPluginVersion = call_user_func("$pendingPluginClass::getVersion");
//				$pendingPluginVersion = $pendingPluginClass::getVersion();
				if(!$pendingPluginVersion->isCompatible($pendingPluginMinVersion))
					return false;
			}
			
			// adds tested plugin name to the list of validated in order to avoid endless recursion
			$tempValidatedPlugins = $validatedPlugins;
			// TODO remove call_user_func after moving to php 5.3
			$tempValidatedPlugins[] = call_user_func("$pluginClass::getPluginName");
//			$tempValidatedPlugins[] = $pluginClass::getPluginName();
			if(!self::isValid($pendingPluginClass, $tempValidatedPlugins))
				return false;
				
			// adds the last tested dependency plugin to the valid list
			$validatedPlugins[] = $pendingPluginName;
		}
		
		return true;
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
			$pluginName = call_user_func("$pluginClass::getPluginName");
//			$pluginName = $pluginClass::getPluginName();
			self::$plugins[$pluginName] = $pluginClass;
		}
			
		return self::$plugins;
	}
}
