<?php
/**
 * @package plugins.clamAvScanEngine
 * @subpackage api.enum
 */
class ClamAVScanEngineVirusScanEngineType implements IKalturaPluginEnum, VirusScanEngineType
{
	const CLAMAV_SCAN_ENGINE = 'ClamAV';
	
	public static function getAdditionalValues()
	{
		return array(
			'CLAMAV_SCAN_ENGINE' => self::CLAMAV_SCAN_ENGINE,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array();
	}
}
