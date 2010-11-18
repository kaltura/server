<?php
/**
 * @package api
 * @subpackage enum
 */
class SymantecScanEngineVirusScanEngineType extends KalturaVirusScanEngineType
{
	const SYMANTEC_SCAN_ENGINE = 'SymantecScanEngine';
	
	/**
	 * @var SymantecScanEngineVirusScanEngineType
	 */
	protected static $instance;

	/**
	 * @return SymantecScanEngineVirusScanEngineType
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new SymantecScanEngineVirusScanEngineType();
			
		return self::$instance;
	}
	
	public static function getAdditionalValues()
	{
		return array(
			'SYMANTEC_SCAN_ENGINE' => self::SYMANTEC_SCAN_ENGINE
		);
	}
	
	public function getPluginName()
	{
		return SymantecScanEnginePlugin::getPluginName();
	}
}
