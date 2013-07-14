<?php
/**
 * @package plugins.symantecScanEngine
 * @subpackage model.enum
 */
class SymantecScanEngineVirusScanEngineType implements IKalturaPluginEnum, VirusScanEngineType
{
	const SYMANTEC_SCAN_ENGINE = 'SymantecScanEngine';
	const SYMANTEC_SCAN_JAVA_ENGINE = 'SymantecScanJavaEngine';
	const SYMANTEC_SCAN_DIRECT_ENGINE = 'SymantecScanDirectEngine';
	
	public static function getAdditionalValues()
	{
		return array(
			'SYMANTEC_SCAN_ENGINE' => self::SYMANTEC_SCAN_ENGINE,
			'SYMANTEC_SCAN_JAVA_ENGINE' => self::SYMANTEC_SCAN_JAVA_ENGINE,
			'SYMANTEC_SCAN_DIRECT_ENGINE' => self::SYMANTEC_SCAN_DIRECT_ENGINE,
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
