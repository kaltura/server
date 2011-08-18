<?php
/**
 * @package api
 * @subpackage enum
 */
abstract class KalturaDynamicEnum extends KalturaStringEnum implements IKalturaDynamicEnum
{
	public static function mergeDescriptions($baseEnumName, array $descriptions)
	{
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaEnumerator');
		foreach($pluginInstances as $pluginInstance)
		{
			$pluginName = $pluginInstance->getPluginName();
			$enums = $pluginInstance->getEnums($baseEnumName);
			foreach($enums as $enum)
			{
				// TODO remove call_user_func after moving to php 5.3
				$additionalDescriptions = call_user_func(array($enum, 'getAdditionalDescriptions'));
				// $additionalDescriptions = $enum::getAdditionalDescriptions();
				$descriptions = array_merge($descriptions, $additionalDescriptions);
			}
		}
		return $descriptions;
	}
}
