<?php
/**
 * @package plugins.scheduleDropFolder
 */
class DropFolderSchedulePlugin extends KalturaPlugin implements IKalturaEnumerator, IKalturaObjectLoader, IKalturaEventConsumers
{
	const PLUGIN_NAME = 'scheduleDropFolder';
	const DROP_FOLDER_EVENTS_CONSUMER = 'kDropFolderICalEventsConsumer';
	
	/**
	 * Returns the plugin name
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/*
	 * (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn()
	{
		$bulkUploadDependency = new KalturaDependency(BulkUploadSchedulePlugin::PLUGIN_NAME);
		$dropFolderDependency = new KalturaDependency(DropFolderPlugin::PLUGIN_NAME);
		
		return array($bulkUploadDependency, $dropFolderDependency);
	}
	
	/**
	 *
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('DropFolderFileHandlerScheduleType');
		
		if($baseEnumName == 'DropFolderFileHandlerType')
			return array('DropFolderFileHandlerScheduleType');
		
		return array();
	}

	/**
	 * {@inheritDoc}
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'Kaltura_Client_DropFolder_Type_DropFolderFileHandlerConfig' && $enumValue == Kaltura_Client_DropFolder_Enum_DropFolderFileHandlerType::ICAL)
		{
			return new Kaltura_Client_ScheduleDropFolder_Type_DropFolderICalBulkUploadFileHandlerConfig();
		}
		
		if($baseClass == 'Form_BaseFileHandlerConfig' && $enumValue == Kaltura_Client_DropFolder_Enum_DropFolderFileHandlerType::ICAL)
		{
			return new Form_ICalFileHandlerConfig();
		}
	}

	/**
	 * {@inheritDoc}
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		return null;
	}

	/**
	 * {@inheritDoc}
	 * @see IKalturaEventConsumers::getEventConsumers()
	 */
	public static function getEventConsumers()
	{
		return array(
			self::DROP_FOLDER_EVENTS_CONSUMER,
		);
	}
	
	/**
	 *
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getFileHandlerTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('DropFolderFileHandlerType', $value);
	}
	
	/**
	 *
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
}
