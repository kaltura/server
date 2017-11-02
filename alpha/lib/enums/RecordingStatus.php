<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface RecordingStatus extends BaseEnum
{
	const STOPPED = 0;
	const PAUSED = 1;
	const ACTIVE = 2;
	const DISABLED = 3;
}