<?php
/**
 * @package plugins.cuePoint
 * @subpackage api.errors
 */
class KalturaCuePointErrors extends KalturaErrors
{
	const END_TIME_CANNOT_BE_LESS_THAN_START_TIME = "END_TIME_CANNOT_BE_LESS_THAN_START_TIME,end time cannot be less than start time";
	const START_TIME_CANNOT_BE_EMPTY = "START_TIME_CANNOT_BE_EMPTY,start time cannot be null";
	const END_TIME_WITHOUT_START_TIME = "END_TIME_WITHOUT_START_TIME,cannot provide end time without start time";
	const START_TIME_IS_BIGGER_THAN_ENTRY_END_TIME = "START_TIME_IS_BIGGER_THAN_ENTRY_END_TIME,start time of the cue point [%s] cannot be bigger than entry end time [%s]";
	const END_TIME_IS_BIGGER_THAN_ENTRY_END_TIME = "END_TIME_IS_BIGGER_THAN_ENTRY_END_TIME,end time of the cue point [%s] cannot be bigger than entry end time [%s]";
	const START_TIME_CANNOT_BE_LESS_THAN_0 = "START_TIME_CANNOT_BE_LESS_THAN_0,start time cannot be less than 0";
	const CANNOT_UPDATE_ENTRY_ID = "CANNOT_UPDATE_ENTRY_ID,cannot update cue point's entry id";
}