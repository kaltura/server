<?php
/**
 * @package plugins.eventNotification
 * @subpackage model.enum
 */ 
interface EventNotificationTemplateStatus extends BaseEnum
{
	const DISABLED = 1;
	const ACTIVE = 2;
	const DELETED = 3;
}