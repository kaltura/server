<?php
/**
 * @package plugins.venodr
 * @subpackage model.enum
 */
interface kEventType extends BaseEnum
{
	const NOT_IMPLEMENTED_EVENT_TYPE = 0;
	const RECORDING_VIDEO_COMPLETED = 1;
	const RECORDING_TRANSCRIPT_COMPLETED = 2;
}