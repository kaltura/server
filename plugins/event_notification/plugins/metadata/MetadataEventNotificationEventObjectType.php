<?php
/**
 * @package plugins.metadataEventNotifications
 * @subpackage lib
 */
class MetadataEventNotificationEventObjectType implements IKalturaPluginEnum, EventNotificationEventObjectType
{
	const METADATA = 'Metadata';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'METADATA' => self::METADATA,
		);
	}
	
	/**
	* @return array
	*/
	public static function getAdditionalDescriptions()
	{
		return array(
			MetadataEventNotificationsPlugin::getApiValue(self::METADATA) => 'Custom metadata object',
		);
	}
}
