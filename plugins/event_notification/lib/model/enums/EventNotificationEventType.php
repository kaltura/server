<?php
/**
 * @package plugins.eventNotification
 * @subpackage model.enum
 *
 * Constant values naming convention is important, the classes are loaded accordingly.
 */
interface EventNotificationEventType extends BaseEnum
{
	const BATCH_JOB_STATUS = 1;
	const OBJECT_ADDED = 2;
	const OBJECT_CHANGED = 3;
	const OBJECT_COPIED = 4;
	const OBJECT_CREATED = 5;
	const OBJECT_DATA_CHANGED = 6;
	const OBJECT_DELETED = 7;
	const OBJECT_ERASED = 8;
	const OBJECT_READY_FOR_REPLACMENT = 9;
	const OBJECT_SAVED = 10;
	const OBJECT_UPDATED = 11;
	const OBJECT_REPLACED = 12;
	const OBJECT_READY_FOR_INDEX = 13;
}