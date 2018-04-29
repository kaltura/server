<?php
/**
 * @package Core
 * @subpackage model.enum
 */
interface EntryServerNodeStatus extends BaseEnum
{
	const STOPPED = 0;
	const PLAYABLE = 1;
	const BROADCASTING = 2;
	const AUTHENTICATED = 3;
	const MARKED_FOR_DELETION = 4;
}