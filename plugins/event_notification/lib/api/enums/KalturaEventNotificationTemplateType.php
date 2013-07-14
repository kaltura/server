<?php
/**
 * @package plugins.eventNotification
 * @subpackage api.enum
 * @see EventNotificationTemplateType
 */
class KalturaEventNotificationTemplateType extends KalturaDynamicEnum implements EventNotificationTemplateType
{
	public static function getEnumClass()
	{
		return 'EventNotificationTemplateType';
	}
}