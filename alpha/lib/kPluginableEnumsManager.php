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
	 * @var array
	 */
	protected static $apiMap;
	
	/**
	 * Enable dynamic creation of new values
	 * @var bool
	 */
	protected static $createNew = false;
	
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
	
	public static function genericApiToCore($genericValue)
	{
	    list($type, $value) = explode(IKalturaEnumerator::PLUGIN_VALUE_DELIMITER, $genericValue, 2);
	    
	    return self::apiToCore($type, $value);
	}

	public static function apiToCore($type, $value)
	{		
		if(is_null($value))
			return null;
			
		$split = explode(IKalturaEnumerator::PLUGIN_VALUE_DELIMITER, $value);
		if(count($split) == 1)
		{
			if(!preg_match('/[\w\d]+/', $value))
				throw new kCoreException("Dynamic enum invalid format [$value] for type [$type]", kCoreException::INVALID_ENUM_FORMAT);
				
			return $value;
		}
	
		if(!preg_match('/\w[\w\d]+\.[\w\d]+/', $value))
			throw new kCoreException("Dynamic enum invalid format [$value] for type [$type]", kCoreException::INVALID_ENUM_FORMAT);
			
		$typeMap = self::getApiMap($type);
		if($typeMap && isset($typeMap[$value]))
			return $typeMap[$value];
			
		list($pluginName, $valueName) = $split;
		
		$dynamicEnum = DynamicEnumPeer::retrieveByPluginConstant($type, $valueName, $pluginName);
		if(!$dynamicEnum)
		{
			if(!self::$createNew)
				throw new kCoreException("Dynamic enum not found [$value] for type [$type]", kCoreException::ENUM_NOT_FOUND);
				
			$dynamicEnum = new DynamicEnum();
			$dynamicEnum->setEnumName($type);
			$dynamicEnum->setValueName($valueName);
			$dynamicEnum->setPluginName($pluginName);
			$dynamicEnum->save();
		}
		self::reloadMaps();
		
		return $dynamicEnum->getId();
	}

	/**
	 * @param string $value
	 * @return IKalturaEnumerator
	 */
	public static function getPlugin($value)
	{
		$split = explode(IKalturaEnumerator::PLUGIN_VALUE_DELIMITER, $value, 2);
		if(count($split) == 1)
			return null;
			
		list($pluginName, $valueName) = $split;
		$plugin = KalturaPluginManager::getPluginInstance($pluginName);
		if($plugin && $plugin instanceof IKalturaEnumerator)
			return $plugin;
			
		return null;
	}
	
	
	/**
	 * Enable dynamic creation of new values
	 */
	public static function enableNewValues()
	{
		self::$createNew = true;
	}
}