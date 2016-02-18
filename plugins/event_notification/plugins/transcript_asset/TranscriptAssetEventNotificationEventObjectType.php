<?php
/**
 * @package plugins.transcriptAssetEventNotifications
 * @subpackage lib
 */
class TranscriptAssetEventNotificationEventObjectType implements IKalturaPluginEnum, EventNotificationEventObjectType
{
	const TRANSCRIPT_ASSET = 'TranscriptAsset';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'TRANSCRIPT_ASSET' => self::TRANSCRIPT_ASSET,
		);
	}
	
	/**
	* @return array
	*/
	public static function getAdditionalDescriptions()
	{
		return array(
			TranscriptAssetEventNotificationsPlugin::getApiValue(self::TRANSCRIPT_ASSET) => 'Transcript asset object',
		);
	}	
}
