<?php
/**
 * @package plugins.eventCuePoint
 * @subpackage model.enum
 */
interface EventType extends BaseEnum
{
	const BROADCAST_START = 1;
	const BROADCAST_END = 2;
	const MUSIC = 3;
	const CONNECTIVITY = 4;
}