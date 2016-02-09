<?php
/**
 * @package Core
 * @subpackage model.enum
 */
interface EntryServerNodeStatus extends BaseEnum{

	const PLAYABLE = 1;
	const AUTHENTICATED = 2;
	const BROADCASTING = 3;
	const STOPPED = 4;


}