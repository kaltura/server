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
		return kPluginableEnumsManager::apiToCore($type, $value);
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
