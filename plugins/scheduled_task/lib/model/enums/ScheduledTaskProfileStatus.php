<?php
/**
 * @package plugins.scheduledTask
 * @subpackage model.enum
 */ 
interface ScheduledTaskProfileStatus extends BaseEnum
{
	const DISABLED = 1;
	const ACTIVE = 2;
	const DELETED = 3;
}