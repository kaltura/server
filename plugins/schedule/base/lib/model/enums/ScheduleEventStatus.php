<?php
/**
 * @package plugins.schedule
 * @subpackage model.enum
 */
interface ScheduleEventStatus extends BaseEnum
{
	const CANCELLED = 1;
	const ACTIVE = 2;
	const DELETED = 3;
}