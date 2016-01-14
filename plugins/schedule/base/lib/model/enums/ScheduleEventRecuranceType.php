<?php
/**
 * @package plugins.schedule
 * @subpackage model.enum
 */
interface ScheduleEventRecuranceType extends BaseEnum
{
	const NONE = 0;
	const RECURRING = 1;
	const RECURRENCE = 2;
}