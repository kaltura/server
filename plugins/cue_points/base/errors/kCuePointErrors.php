<?php
/**
 * @package plugins.cuePoint
 * @subpackage api.errors
 */
interface kCuePointErrors
{
	const END_TIME_CANNOT_BE_LESS_THAN_START_TIME = "END_TIME_CANNOT_BE_LESS_THAN_START_TIME;;End time cannot be less than start time";
	const START_TIME_CANNOT_BE_EMPTY = "START_TIME_CANNOT_BE_EMPTY;;Start time cannot be null";
	const END_TIME_WITHOUT_START_TIME = "END_TIME_WITHOUT_START_TIME;;Cannot provide end time without start time";
	const START_TIME_IS_BIGGER_THAN_ENTRY_END_TIME = "START_TIME_IS_BIGGER_THAN_ENTRY_END_TIME;CUE_POINT_ID,END_TIME;Start time of the cue point [@CUE_POINT_ID@] cannot be bigger than entry end time [@END_TIME@]";
	const END_TIME_IS_BIGGER_THAN_ENTRY_END_TIME = "END_TIME_IS_BIGGER_THAN_ENTRY_END_TIME;CUE_POINT_ID,END_TIME;End time of the cue point [@CUE_POINT_ID@] cannot be bigger than entry end time [@END_TIME@]";
	const START_TIME_CANNOT_BE_LESS_THAN_0 = "START_TIME_CANNOT_BE_LESS_THAN_0;;Start time cannot be less than 0";
	const CANNOT_UPDATE_ENTRY_ID = "CANNOT_UPDATE_ENTRY_ID;;Cannot update cue point's entry id";
	
	const PARENT_ANNOTATION_NOT_FOUND = "PARENT_ANNOTATION_NOT_FOUND;CUE_POINT_ID;Parent annotation id [@CUE_POINT_ID@] not found";
	const PARENT_ANNOTATION_DO_NOT_BELONG_TO_THE_SAME_ENTRY = "PARENT_ANNOTATION_DO_NOT_BELONG_TO_THE_SAME_ENTRY;;parent annotation does not belong to current annotation";
	const PARENT_ANNOTATION_IS_DESCENDANT = "PARENT_ANNOTATION_IS_DESCENDANT;PARENT_CUE_POINT_ID,CUE_POINT_ID;Parent annotation [@PARENT_CUE_POINT_ID@] is a child or a sub child of this annotation [@CUE_POINT_ID@] and therefor cannot be a parent";
	
	const XML_FILE_NOT_FOUND = "XML_FILE_NOT_FOUND;;XML file not found";
	const XML_INVALID = "XML_INVALID;;XML is invalid";
}