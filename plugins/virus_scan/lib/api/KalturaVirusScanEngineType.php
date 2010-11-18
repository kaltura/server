<?php
class KalturaVirusScanEngineType extends KalturaDynamicEnum implements VirusScanEngineType
{
	/**
	 * @return string
	 */
	public static function getEnumClass()
	{
		return 'VirusScanEngineType';
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