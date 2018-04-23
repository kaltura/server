<?php
/**
 * Enable Conference servers
 * @package plugins.conference
 */
class ConferencePlugin extends KalturaPlugin implements IKalturaObjectLoader, IKalturaEnumerator, IKalturaServices
{
	const PLUGIN_NAME = 'conference';

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
			return array('ConferenceServerNodeType','ConferenceEntryServerNodeType');
	
		if($baseEnumName == 'serverNodeType')
			return array('ConferenceServerNodeType');

		if($baseEnumName == 'entryType')
			return array('ConferenceEntryServerNodeType');
			
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'KalturaServerNode' && $enumValue == self::getCoreValue('serverNodeType',ConferenceServerNodeType::CONFERENCE_SERVER))
			return new KalturaConferenceServerNode();
		if($baseClass == 'KalturaEntryServerNode' && $enumValue == self::getCoreValue('EntryServerNodeType',ConferenceEntryServerNodeType::CONFERENCE_ENTRY_SERVER))
			return new KalturaConferenceEntryServerNode();

	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'ServerNode' && $enumValue == self::getCoreValue('serverNodeType',ConferenceServerNodeType::CONFERENCE_SERVER))
			return 'ConferenceServerNode';
		if($baseClass == 'EntryServerNode' && $enumValue == self::getCoreValue('EntryServerNodeType',ConferenceEntryServerNodeType::CONFERENCE_ENTRY_SERVER))
			return 'ConferenceEntryServerNode';
	}

	public static function getCoreValue($type, $valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore($type, $value);
	}

	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}

	public static function getServicesMap()
	{
		$map = array(
			'conference' => 'ConferenceService',
		);
		return $map;

	}


}
