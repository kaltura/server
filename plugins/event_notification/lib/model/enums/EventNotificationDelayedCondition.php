<?php
/**
 * @package plugins.eventNotification
 * @subpackage model.enum
 */ 
interface EventNotificationDelayedCondition extends BaseEnum
{
	const NONE = 0;
	const PENDING_ENTRY_READY = 1;
}
