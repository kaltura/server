<?php
/**
 * @package Core
 * @subpackage enum
 */ 
class kPluginableEnumsManager
{
	/**
	 * All dynamic enums, mapped on the id as key
	 * @var array
	 */
	protected static $coreMap;
	
	/**
	 * All dynamic enums, mapped on the api name as key
	 * @var unknown_type
	 */
	protected static $apiMap;
	
	protected static function reloadMaps()
	{
		self::loadApiMap(false);
		self::loadCoreMap(false);
	}
	
	protected static function loadCoreMap($useCache = true)
	{
		$coreCachePath = kConf::get('cache_root_path') . '/CorePluginableEnums.cache';
		if($useCache && file_exists($coreCachePath))
		{
			self::$coreMap = unserialize(file_get_contents($coreCachePath));
			return;
		}
		
		self::$coreMap = array();
		$dynamicEnums = DynamicEnumPeer::doSelect(new Criteria());
		foreach($dynamicEnums as $dynamicEnum)
		{
			$dynamicEnumId = $dynamicEnum->getId();
			$dynamicEnumType = $dynamicEnum->getEnumName();
			$dynamicEnumApiName = $dynamicEnum->getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $dynamicEnum->getValueName();
			if(!isset(self::$coreMap[$dynamicEnumType]))
				self::$coreMap[$dynamicEnumType] = array();
				
			self::$coreMap[$dynamicEnumType][$dynamicEnumId] = $dynamicEnumApiName;
		}
		file_put_contents($coreCachePath, serialize(self::$coreMap));
	}
	
	protected static function loadApiMap($useCache = true)
	{
		$apiCachePath = kConf::get('cache_root_path') . '/ApiPluginableEnums.cache';
		if($useCache && file_exists($apiCachePath))
		{
			self::$apiMap = unserialize(file_get_contents($apiCachePath));
			return;
		}
		
		self::$apiMap = array();
		$dynamicEnums = DynamicEnumPeer::doSelect(new Criteria());
		foreach($dynamicEnums as $dynamicEnum)
		{
			$dynamicEnumId = $dynamicEnum->getId();
			$dynamicEnumType = $dynamicEnum->getEnumName();
			$dynamicEnumApiName = $dynamicEnum->getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $dynamicEnum->getValueName();
			if(!isset(self::$apiMap[$dynamicEnumType]))
				self::$apiMap[$dynamicEnumType] = array();
				
			self::$apiMap[$dynamicEnumType][$dynamicEnumApiName] = $dynamicEnumId;
		}
		file_put_contents($apiCachePath, serialize(self::$apiMap));
	}
	
	public static function getCoreMap($type = null)
	{
		if(!count(self::$coreMap))
			self::loadCoreMap();
			
		if(is_null($type))
			return self::$coreMap;
			
		if(isset(self::$coreMap[$type]))
			return self::$coreMap[$type];
			
		return null;
	}
	
	public static function getApiMap($type = null)
	{
		if(!count(self::$apiMap))
			self::loadApiMap();
			
		if(is_null($type))
			return self::$apiMap;
			
		if(isset(self::$apiMap[$type]))
			return self::$apiMap[$type];
			
		return null;
	}
	
	public static function coreValues($type)
	{
		$reflect = new ReflectionClass($type);
		$values = $reflect->getConstants();
		
		$typeMap = self::getApiMap($type);
		foreach($typeMap as $apiValue => $coreValue)
			if(!in_array($coreValue, $values))
				$values[$apiValue] = $coreValue;
				
		return $values;
	}
	
	public static function coreToApi($type, $value)
	{
		$typeMap = self::getCoreMap($type);
		if(!$typeMap)
			return $value; 
		
		if(isset($typeMap[$value]))
			return $typeMap[$value];
			
		return $value;
	}

	public static function apiToCore($type, $value)
	{
		$split = explode(IKalturaEnumerator::PLUGIN_VALUE_DELIMITER, $value, 2);
		if(count($split) == 1)
			return $value;
			
		$typeMap = self::getApiMap($type);
		if($typeMap && isset($typeMap[$value]))
			return $typeMap[$value];
			
		list($pluginName, $valueName) = $split;
		
		$dynamicEnum = new DynamicEnum();
		$dynamicEnum->setEnumName($type);
		$dynamicEnum->setValueName($valueName);
		$dynamicEnum->setPluginName($pluginName);
		$dynamicEnum->save();
		
		self::reloadMaps();
		
		return $dynamicEnum->getId();
	}
}