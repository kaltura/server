<?php
/**
 * @package plugins.pushNotification
 * @subpackage model.enum
 *
 * Constant values describing the suported commands allowed to be sent to the push server
 */
interface PushNotificationCommandType extends BaseEnum
{
	const CLEAR_QUEUE = "CLEAR_QUEUE";
	const NOTIFY_USER = "NOTIFY_USER";
}