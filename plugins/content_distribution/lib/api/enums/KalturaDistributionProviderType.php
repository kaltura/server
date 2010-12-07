<?php
class KalturaDistributionProviderType extends KalturaDynamicEnum implements DistributionProviderType
{
	public static function getEnumClass()
	{
		return 'DistributionProviderType';
	}
	
	/**
	 * @param string $const
	 * @param string $type
	 * @return int
	 */
	public static function getCoreValue($valueName, $type = __CLASS__)
	{
		return parent::getCoreValue($valueName, $type);
	}
}