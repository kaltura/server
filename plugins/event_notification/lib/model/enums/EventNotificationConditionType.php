<?php
/**
 * @package plugins.metadata
 * @subpackage model.enum
 */
class EventNotificationConditionType implements IKalturaPluginEnum, ConditionType
{
	const EVENT_NOTIFICATION_FIELD = 'BooleanField';
	
	public static function getAdditionalValues()
	{
		return array(
			'EVENT_NOTIFICATION_FIELD' => self::EVENT_NOTIFICATION_FIELD,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array(
			EventNotificationPlugin::getApiValue(self::EVENT_NOTIFICATION_FIELD) => 'Evaluates boolean dynamic field according to scope.',
		);
	}
}
