<?php
/**
 * @package plugins.eventNotification
 * @subpackage model.enum
 */ 
class EventNotificationBatchType implements IKalturaPluginEnum, BatchJobType
{
	const EVENT_NOTIFICATION_HANDLER = 'EventNotificationHandler';
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues()
	{
		return array(
			'EVENT_NOTIFICATION_HANDLER' => self::EVENT_NOTIFICATION_HANDLER,
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions() 
	{
		return array();
	}

}
