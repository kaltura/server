<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface EntryServerNodeRecordingStatus extends BaseEnum
{
	const STOPPED = 0;
	const RECORDING = 1;
	const DONE = 2;
}