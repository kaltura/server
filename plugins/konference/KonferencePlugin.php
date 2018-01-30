<?php
/**
 * Enable Conference servers
 * @package plugins.konference
 */
class KonferencePlugin extends KalturaPlugin implements IKalturaObjectLoader, IKalturaEnumerator, IKalturaServices
{
	const PLUGIN_NAME = 'konference';

	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}

	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('ConferenceServerNodeType');
	
		if($baseEnumName == 'serverNodeType')
			return array('ConferenceServerNodeType');
			
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'KalturaServerNode' && $enumValue == self::getConferenceCoreValue(ConferenceServerNodeType::CONFERENCE_SERVER))
			return new KalturaConferenceServerNode();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'ServerNode' && $enumValue == self::getConferenceCoreValue(ConferenceServerNodeType::CONFERENCE_SERVER))
			return 'ConferenceServerNode';
		if($baseClass == 'EntryServerNode' && $enumValue == self::getConferenceCoreValue(ConferenceServerNodeType::CONFERENCE_SERVER))
			return 'ConferenceEntryServerNode';
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaCuePoint::getCuePointTypeCoreValue()
	 */
	public static function getConferenceCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('serverNodeType', $value);
	}

	public static function getServicesMap()
	{
		$map = array(
			'conference' => 'ConferenceService',
		);
		return $map;

	}


}
