<?php
/**
 * @package plugins.symantecScanEngine
 */
class SymantecScanEnginePlugin extends KalturaPlugin implements IKalturaPending, IKalturaEnumerator, IKalturaObjectLoader
{
	const PLUGIN_NAME = 'symantecScanEngine';
	const VIRUS_SCAN_PLUGIN_NAME = 'virusScan';
	
	/**
	 * @return array<KalturaDependency>
	 */
	public static function dependsOn()
	{
		return array(new KalturaDependency(self::VIRUS_SCAN_PLUGIN_NAME));
	}

	/**
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('SymantecScanEngineVirusScanEngineType');
			
		if($baseEnumName == 'VirusScanEngineType')
			return array('SymantecScanEngineVirusScanEngineType');
			
		return array();
	}

	/**
	 * 
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'VirusScanEngine' && $enumValue == KalturaVirusScanEngineType::SYMANTEC_SCAN_ENGINE)
			return new SymantecScanEngine();

		return null;
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @return string
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'VirusScanEngine' && $enumValue == KalturaVirusScanEngineType::SYMANTEC_SCAN_ENGINE)
			return 'SymantecScanEngine';

		return null;
	}
}