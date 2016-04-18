<?php
/**
 * @package plugins.schedule
 * @subpackage model.enum
 */
interface ScheduleEventRecurrenceFrequency extends BaseEnum
{
	const SECONDLY = 'seconds';
	const MINUTELY = 'minutes';
	const HOURLY = 'hours';
	const DAILY = 'days';
	const WEEKLY = 'weeks';
	const MONTHLY = 'months';
	const YEARLY = 'years';
}