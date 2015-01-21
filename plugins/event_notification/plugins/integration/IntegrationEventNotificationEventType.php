<?php
/**
 * @package plugins.integrationEventNotifications
 * @subpackage lib
 */
class IntegrationEventNotificationEventType implements IKalturaPluginEnum, EventNotificationEventType
{
	const INTEGRATION_JOB_CLOSED = 'INTEGRATION_JOB_CLOSED';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'INTEGRATION_JOB_CLOSED' => self::INTEGRATION_JOB_CLOSED,
		);
	}
	
	/**
	* @return array
	*/
	public static function getAdditionalDescriptions()
	{
		return array(
			IntegrationEventNotificationsPlugin::getApiValue(self::INTEGRATION_JOB_CLOSED) => 'Integration Job Closed',
		);
	}
}
