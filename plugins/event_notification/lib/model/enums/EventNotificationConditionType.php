<?php
/**
 * @package plugins.metadata
 * @subpackage model.enum
 */
class EventNotificationConditionType implements IKalturaPluginEnum, ConditionType
{
	const EVENT_NOTIFICATION_FIELD = 'BooleanField';
	const EVENT_NOTIFICATION_OBJECT_CHANGED = 'ObjectChanged';
	
	public static function getAdditionalValues()
	{
		return array(
			'EVENT_NOTIFICATION_FIELD' => self::EVENT_NOTIFICATION_FIELD,
			'EVENT_NOTIFICATION_OBJECT_CHANGED' => self::EVENT_NOTIFICATION_OBJECT_CHANGED,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array(
			EventNotificationPlugin::getApiValue(self::EVENT_NOTIFICATION_FIELD) => 'Evaluates boolean dynamic field according to scope.',
			EventNotificationPlugin::getApiValue(self::EVENT_NOTIFICATION_OBJECT_CHANGED) => 'Return true if object changed and defined columns modified.',
		);
	}
}
