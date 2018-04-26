<?php
/**
 * @package Core
 * @subpackage model.enum
 */
interface EntryServerNodeStatus extends BaseEnum
{
	const ERROR = -1;
	const STOPPED = 0;
	const PLAYABLE = 1;
	const BROADCASTING = 2;
	const AUTHENTICATED = 3;
	const MARKED_FOR_DELETION = 4;

	const TASK_PENDING = 5;
	const TASK_QUEUED = 6;
	const TASK_PROCESSING = 7;
	const TASK_UPLOADING = 8;
}
