<?php
/**
 * @package plugins.clamAvScanEngine
 */
class ClamAVScanEnginePlugin extends KalturaPlugin implements IKalturaPending, IKalturaEnumerator, IKalturaObjectLoader
{
	const PLUGIN_NAME = 'clamAVScanEngine';
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
			return array('ClamAVScanEngineVirusScanEngineType');
			
		if($baseEnumName == 'VirusScanEngineType')
			return array('ClamAVScanEngineVirusScanEngineType');
			
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
		$objectClass = self::getObjectClass($baseClass, $enumValue);
		
		if (is_null($objectClass)) {
			return null;
		}
		
		if (!is_null($constructorArgs))
		{
			$reflect = new ReflectionClass($objectClass);
			return $reflect->newInstanceArgs($constructorArgs);
		}
		else
		{
			return new $objectClass();
		}
	}
	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @return string
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'VirusScanEngine')
		{
			if($enumValue == KalturaVirusScanEngineType::CLAMAV_SCAN_ENGINE)
				return 'ClamAVScanEngine';
		}

		return null;
	}
}