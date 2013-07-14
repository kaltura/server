<?php
/**
 * @package plugins.emailNotification
 * @subpackage model.enum
 */ 
interface EmailNotificationTemplatePriority extends BaseEnum
{
	const HIGH = 1;
	const NORMAL = 3;
	const LOW = 5;
}