<?php
/**
 * @package plugins.schedule
 * @subpackage model.enum
 */
interface ScheduleEventType extends BaseEnum
{
	const RECORD = 1;
	const LIVE_STREAM = 2;
	const BLACKOUT = 3;
	const MEETING = 4;
	const LIVE_REDIRECT = 5;
	const VOD = 6;
}