<?php

/**
 * Subclass for representing a row from the 'scheduler_status' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class SchedulerStatus extends BaseSchedulerStatus
{
	const RUNNING_BATCHES_COUNT = 1;
	const RUNNING_BATCHES_CPU = 2;
	const RUNNING_BATCHES_MEMORY = 3;
	const RUNNING_BATCHES_NETWORK = 4;
	const RUNNING_BATCHES_DISC_IO = 5;
	const RUNNING_BATCHES_DISC_SPACE = 6;
	const RUNNING_BATCHES_IS_RUNNING = 7;
}
