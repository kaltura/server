<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaBatchJobStatus extends KalturaEnum
{
	const PENDING = 0; // in queue
	const QUEUED = 1; // in process
	const PROCESSING = 2; // in process
	const PROCESSED = 3; // in process
	const MOVEFILE = 4; // in process
	const FINISHED = 5; // done
	const FAILED = 6; // done
	const ABORTED = 7; // done
	const ALMOST_DONE = 8; // in process
	const RETRY = 9;  // in queue
	const FATAL = 10; // done
	const DONT_PROCESS = 11; // done
}
?>