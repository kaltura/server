<?php
/**
 * @package plugins.schedule
 * @subpackage model.enum
 */
interface ScheduleResourceStatus extends BaseEnum
{
	const DISABLED = 1;
	const ACTIVE = 2;
	const DELETED = 3;
}