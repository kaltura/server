<?php
/**
 * @package plugins.vendor
 * @subpackage model.enum
 */
interface kEventType extends BaseEnum
{
	const NOT_IMPLEMENTED_EVENT_TYPE = 0;
	const RECORDING_VIDEO_COMPLETED = 1;
	const RECORDING_TRANSCRIPT_COMPLETED = 2;
	const NEW_RECORDING_VIDEO_COMPLETED = 3;
	const NEW_RECORDING_TRANSCRIPT_COMPLETED = 4;
}