<?php
/**
 * @package plugins.schedule
 * @subpackage model.enum
 */
interface ScheduleResourceType extends BaseEnum
{
	const LOCATION = 1;
	const LIVE_ENTRY = 2;
	const CAMERA = 3;
}