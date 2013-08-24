<?php
/**
 * @package plugins.contentDistributionEventNotifications
 * @subpackage lib
 */
class ContentDistributionEventNotificationEventObjectType implements IKalturaPluginEnum, EventNotificationEventObjectType
{
	const DISTRIBUTION_PROFILE = 'DistributionProfile';
	const ENTRY_DISTRIBUTION = 'EntryDistribution';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'DISTRIBUTION_PROFILE' => self::DISTRIBUTION_PROFILE,
			'ENTRY_DISTRIBUTION' => self::ENTRY_DISTRIBUTION,
		);
	}
	
	/**
	* @return array
	*/
	public static function getAdditionalDescriptions()
	{
		return array(
			ContentDistributionEventNotificationsPlugin::getApiValue(self::DISTRIBUTION_PROFILE) => 'Distribution profile object',
			ContentDistributionEventNotificationsPlugin::getApiValue(self::ENTRY_DISTRIBUTION) => 'Entry - Distribution object',
		);
	}
}
