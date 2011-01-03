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
}