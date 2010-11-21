<?php

class kPluginableEnumsManager
{
	public static function coreToApi($type, $value)
	{
		// TODO remove call_user_func after moving to php 5.3
		$baseEnumName = call_user_func("$type::getEnumClass");
//		$baseEnumName = $type::getEnumClass();
	
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaEnumerator');
		foreach($pluginInstances as $pluginInstance)
		{
			$enums = $pluginInstance->getEnums($baseEnumName);
			foreach($enums as $enum)
			{
				// TODO remove call_user_func after moving to php 5.3
				$enumValue = call_user_func("$enum::get")->coreToApi($value);
//				$enumValue = $enum::get()->coreToApi($value);
				if(!is_null($enumValue))
					return $enumValue;
			}
		}
			
		return $value;
	}

	public static function apiToCore($type, $value)
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
}