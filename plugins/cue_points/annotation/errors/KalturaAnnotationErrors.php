<?php
/**
 * @package plugins.annotation
 * @subpackage api.errors
 */
class KalturaAnnotationErrors extends KalturaCuePointErrors
{
	const PARENT_ANNOTATION_NOT_FOUND = "PARENT_ANNOTATION_NOT_FOUND,Parent annotation id \"%s\" not found";
	const PARENT_ANNOTATION_DO_NOT_BELONG_TO_THE_SAME_ENTRY = "PARENT_ANNOTATION_DO_NOT_BELONG_TO_THE_SAME_ENTRY,parent annotation does not belong to current annotation";
	const PARENT_ANNOTATION_IS_DESCENDANT = "PARENT_ANNOTATION_IS_DESCENDANT,parent annotation [%s] is a child or a sub child of this annotation [%s] and therefor cannot be a parent";
}