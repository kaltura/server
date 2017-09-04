<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface EntryServerNodeRecordingStatus extends BaseEnum
{
	const STOPPED = 0;
	const ON_GOING = 1;
	const DONE = 2;
	const DISMISSED = 3;
}