<?php
/**
 * @package plugins.schedule
 * @subpackage model.enum
 */
interface ScheduleEventType extends BaseEnum
{
	const RECORD = 1;
	const LIVE_STREAM = 2;
	const SIMU_LIVE = 3;
}