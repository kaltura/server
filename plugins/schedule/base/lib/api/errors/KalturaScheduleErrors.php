<?php
/**
 * @package plugins.schedule
 * @subpackage api.errors
 */
class KalturaScheduleErrors extends KalturaErrors
{
	const INVALID_SCHEDULE_END_BEFORE_START = "INVALID_SCHEDULE_END_BEFORE_START;START,END;End time [@END@] must be after start time [@START@]";
	const MAX_SCHEDULE_DURATION_REACHED = "MAX_SCHEDULE_DURATION_REACHED;MAX;Maximum schedule duration [@MAX@] reached";
	const MAX_SCHEDULE_DURATION_MUST_MATCH_END_TIME = "MAX_SCHEDULE_DURATION_MUST_MATCH_END_TIME;;Duration must match end time, keep one of them null to calcualte automatically";
}