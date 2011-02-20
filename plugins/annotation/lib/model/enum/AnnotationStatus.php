<?php
/**
 * @package plugins.annotation
 * @subpackage model.enum
 */
interface AnnotationStatus extends BaseEnum
{
	const ANNOTATION_STATUS_READY = 1;
	const ANNOTATION_STATUS_DELETED = 2;
}