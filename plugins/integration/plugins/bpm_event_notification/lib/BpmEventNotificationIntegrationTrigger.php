<?php
/**
 * @package plugins.bpmEventNotificationIntegration
 * @subpackage lib.enum
 */
class BpmEventNotificationIntegrationTrigger implements IKalturaPluginEnum, IntegrationTriggerType
{
	const BPM_EVENT_NOTIFICATION = 'BpmEventNotification';
	
	public static function getAdditionalValues()
	{
		return array(
			'BPM_EVENT_NOTIFICATION' => self::BPM_EVENT_NOTIFICATION,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array();
	}
}
