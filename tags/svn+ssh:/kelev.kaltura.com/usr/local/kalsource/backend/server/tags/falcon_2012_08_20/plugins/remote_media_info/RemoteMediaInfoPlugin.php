<?php
/**
 * @package plugins.remoteMediaInfo
 */
class RemoteMediaInfoPlugin extends KalturaPlugin implements IKalturaObjectLoader, IKalturaEnumerator
{
	const PLUGIN_NAME = 'remoteMediaInfo';
	
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
		if($baseClass == 'KBaseMediaParser' && $enumValue == KalturaMediaParserType::REMOTE_MEDIAINFO)
		{
			$reflectionClass = new ReflectionClass('KRemoteMediaInfoMediaParser');
			return $reflectionClass->newInstanceArgs($constructorArgs);
		}
		
		return null;
	}

	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @return string
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'KBaseMediaParser' && $enumValue == self::getApiValue(RemoteMediaInfoMediaParserType::REMOTE_MEDIAINFO))
			return 'KRemoteMediaInfoMediaParser';
			
		return null;
	}
	
	/**
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('RemoteMediaInfoMediaParserType');
	
		if($baseEnumName == 'mediaParserType')
			return array('RemoteMediaInfoMediaParserType');
			
		return array();
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getMediaParserTypeValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('mediaParserType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
}
