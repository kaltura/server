<?php
/**
 * @package plugins.schedule
 * @subpackage model.enum
 */
interface kScheduleEventConflictType extends BaseEnum
{
	const RESOURCE_CONFLICT = 1;
	const BLACKOUT_CONFLICT = 2;
	const BOTH = 3;
}