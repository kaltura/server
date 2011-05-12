<?php
/**
 * @package plugins.virusScan
 * @subpackage api.objects
 */
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