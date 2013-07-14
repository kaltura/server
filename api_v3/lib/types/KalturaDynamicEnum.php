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
				$additionalDescriptions = $enum::getAdditionalDescriptions();
				foreach($additionalDescriptions as $key => $description)
					$descriptions[$key] = $description;
			}
		}
		return $descriptions;
	}
}
