<?php
/**
 * @package plugins.schedule
 * @subpackage model.enum
 */
interface ScheduleEventRecurrenceFrequency extends BaseEnum
{
	const SECONDLY = DatesGenerator::SECONDLY;
	const MINUTELY = DatesGenerator::MINUTELY;
	const DAILY = DatesGenerator::DAILY;
	const HOURLY = DatesGenerator::HOURLY;
	const WEEKLY = DatesGenerator::WEEKLY;
	const MONTHLY = DatesGenerator::MONTHLY;
	const YEARLY = DatesGenerator::YEARLY;
}