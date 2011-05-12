<?php
/**
 * @package api
 * @subpackage enum
 */
class SymantecScanEngineVirusScanEngineType implements IKalturaPluginEnum, VirusScanEngineType
{
	const SYMANTEC_SCAN_ENGINE = 'SymantecScanEngine';
	
	public static function getAdditionalValues()
	{
		return array(
			'SYMANTEC_SCAN_ENGINE' => self::SYMANTEC_SCAN_ENGINE
		);
	}
}
