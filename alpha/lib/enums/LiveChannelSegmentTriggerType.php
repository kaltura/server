<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface LiveChannelSegmentTriggerType extends BaseEnum
{
	const CHANNEL_RELATIVE = 1;
	const ABSOLUTE_TIME = 2;
	const SEGMENT_START_RELATIVE = 3;
	const SEGMENT_END_RELATIVE = 4;
}
