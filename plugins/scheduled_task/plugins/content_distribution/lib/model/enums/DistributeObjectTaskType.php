<?php
/**
 * @package plugins.scheduledTaskContentDistribution
 * @subpackage model.enum
 */
class DistributeObjectTaskType implements IKalturaPluginEnum, ObjectTaskType
{
	const DISTRIBUTE = 'Distribute';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'DISTRIBUTE' => self::DISTRIBUTE,
		);
	}
	
	/**
	* @return array
	*/
	public static function getAdditionalDescriptions()
	{
		return array(
			ScheduledTaskEventNotificationPlugin::getApiValue(self::DISTRIBUTE) => 'Distribute',
		);
	}
}
