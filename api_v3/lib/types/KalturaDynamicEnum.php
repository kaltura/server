<?php
/**
 * @package api
 * @subpackage enum
 */
abstract class KalturaDynamicEnum extends KalturaStringEnum implements IKalturaDynamicEnum
{
	/**
	 * @return array
	 */
	public static function getAdditionalValues()
	{
		return array();
	}
	
	/**
	 * @return KalturaDynamicEnum
	 */
	public static function get()
	{
		return null;
	}
	
	/**
	 * @return string
	 */
	public function getPluginName()
	{
		return null;
	}
	
	/**
	 * @param string $value
	 * @param string $type
	 * @return int
	 */
	public static function getCoreValue($value, $type = null)
	{
		$split = explode(IKalturaEnumerator::PLUGIN_VALUE_DELIMITER, $value, 2);
		if(count($split) == 1)
			return $value;
			
		list($pluginName, $valueName) = $split;
		
		// TODO remove call_user_func after moving to php 5.3
		$baseEnumName = call_user_func("$type::getEnumClass");
//		$baseEnumName = $type::getEnumClass();
 
		$pluginInstance = KalturaPluginManager::getPluginInstance($pluginName);
		$enums = $pluginInstance->getEnums($baseEnumName);
		
		foreach($enums as $enum)
		{		
			// TODO remove call_user_func after moving to php 5.3
			$enumConstans = call_user_func("$enum::getAdditionalValues");
//			$enumConstans = $enum::getAdditionalValues();
			if(in_array($valueName, $enumConstans))
			{
				// TODO remove call_user_func after moving to php 5.3
				return call_user_func("$enum::get")->coreValue($valueName);
//				return $enum::get()->coreValue($value);
			}
		}
		
		return null;
	}
	
	/**
	 * @param string $const
	 * @return int
	 */
	public function coreValue($const)
	{
		$enumName = $this->getEnumClass();
		$pluginName = $this->getPluginName();
		$dynamicEnum = DynamicEnumPeer::retrieveByPluginConstant($enumName, $const, $pluginName);
		
		if(!$dynamicEnum)
		{
			$dynamicEnum = new DynamicEnum();
			$dynamicEnum->setEnumName($enumName);
			$dynamicEnum->setValueName($const);
			$dynamicEnum->setPluginName($pluginName);
			$dynamicEnum->save();
		}
		
		return $dynamicEnum->getId();
	}

	/**
	 * @param int $id
	 * @return string
	 */
	public function coreToApi($id)
	{
		$enumName = $this->getEnumClass();
		$pluginName = $this->getPluginName();
		$dynamicEnum = DynamicEnumPeer::retrieveByPluginValue($enumName, $id, $pluginName);
		if($dynamicEnum)
			return $this->apiValue($dynamicEnum->getValueName());
			
		return null;
	}

	/**
	 * @param string $const
	 * @return string
	 */
	public function apiValue($const)
	{
		return $this->getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $const; 
	}
}
