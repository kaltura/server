<?php
/**
 * @package plugins.attachmentAssetEventNotifications
 * @subpackage lib
 */
class AttachmentAssetEventNotificationEventObjectType implements IKalturaPluginEnum, EventNotificationEventObjectType
{
	const ATTACHMENT_ASSET = 'AttachmentAsset';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'ATTACHMENT_ASSET' => self::ATTACHMENT_ASSET,
		);
	}
	
	/**
	* @return array
	*/
	public static function getAdditionalDescriptions()
	{
		return array(
			AttachmentAssetEventNotificationsPlugin::getApiValue(self::ATTACHMENT_ASSET) => 'Attachment asset object',
		);
	}	
}
