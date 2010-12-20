<?php
class KalturaAnnotationErrors extends KalturaErrors
{
	const PARENT_ANNOTATION_NOT_FOUND = "PARENT_ANNOTATION_NOT_FOUND,Parent annotation id \"%s\" not found";
	const PARENT_ANNOTATION_DO_NOT_BELONG_TO_THE_SAME_ENTRY = "PARENT_ANNOTATION_DO_NOT_BELONG_TO_THE_SAME_ENTRY,parent annotation does not belong to current annotation";
	const END_TIME_CANNOT_BE_LESS_THEN_START_TIME = "END_TIME_CANNOT_BE_LESS_THEN_START_TIME,end time cannot be less then start time";
	const START_TIME_CANNOT_BE_EMPTY = "START_TIME_CANNOT_BE_EMPTY,start time cannot be null";
	const END_TIME_WITHOUT_START_TIME = "END_TIME_WITHOUT_START_TIME,cannot provide end time without start time";
	const START_TIME_IS_BIGGER_THAN_ENTRY_END_TIME = "START_TIME_IS_BIGGER_THEN_ENTRY_END_TIME,start time of the annotation [%s] cannot be bigger than entry end time [%s]";
	const END_TIME_IS_BIGGER_THAN_ENTRY_END_TIME = "END_TIME_IS_BIGGER_THAN_ENTRY_END_TIME,end time of the annotation [%s] cannot be bigger than entry end time [%s]";
	const START_TIME_CANNOT_BE_LESS_THAN_0 = "START_TIME_CANNOT_BE_LESS_THAN_0,start time cannot be less than 0";
	const PARENT_ANNOTATION_IS_DESCENDANT = "PARENT_ANNOTATION_IS_DESCENDANT,parent annotation [%s] is a child or a sub child of this annotation [%s] and therefor cannot be a parent";
	const CANNOT_UPDATE_ENTRY_ID = "CANNOT_UPDATE_ENTRY_ID,cannot update annotation's entry id";
}