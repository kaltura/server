<?php
/**
 * @package plugins.reach
 * @subpackage api.enum
 * @see EventNotificationEventType
 */
class KalturaReachProfileEventType extends KalturaDynamicEnum implements EventNotificationEventType
{
	public static function getEnumClass()
	{
		return 'EventNotificationEventType';
	}

	public static function getAdditionalDescriptions()
	{
		return array(
			EventNotificationEventType::OBJECT_ADDED => 'Object with all its related files and sub objects added, defined by the developer.',
			EventNotificationEventType::OBJECT_CHANGED => 'Object model changed, automatically raised from the database.',
			EventNotificationEventType::OBJECT_COPIED => 'Object copied into a new object, automatically raised from the database.',
			EventNotificationEventType::OBJECT_CREATED => 'New object created, automatically raised from the database.',
			EventNotificationEventType::OBJECT_DATA_CHANGED => 'Object content changed, defined by the developer.',
			EventNotificationEventType::OBJECT_DELETED => 'Object with all its related files and sub objects deleted, defined by the developer.',
			EventNotificationEventType::OBJECT_ERASED => 'Object erased from the database, automatically raised from the database.',
			EventNotificationEventType::OBJECT_READY_FOR_REPLACMENT => 'Object is ready to be replaced by temporary object content, defined by the developer.',
			EventNotificationEventType::OBJECT_SAVED => 'Object saved to the database, new, existing or deleted, automatically raised from the database.',
			EventNotificationEventType::OBJECT_UPDATED => 'Object with all its related files and sub objects updated, defined by the developer.',
			EventNotificationEventType::OBJECT_REPLACED => 'Object content replaced successfully, defined by the developer.',
		);
	}
}