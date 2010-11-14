<?php
abstract class KalturaPluginEnum implements IKalturaPluginEnum
{
	const PLUGIN_VALUE_DELIMITER = '.';
	
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
		
		return $dynamicEnum->getValue();
	}

	/**
	 * @param int $value
	 * @return string
	 */
	public function coreToApi($value)
	{
		$enumName = $this->getEnumClass();
		$pluginName = $this->getPluginName();
		$dynamicEnum = DynamicEnumPeer::retrieveByPluginValue($enumName, $value, $pluginName);
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
		return $this->getPluginName() . self::PLUGIN_VALUE_DELIMITER . $const; 
	}
}