<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaMailJobStatus extends KalturaEnum
{
	const PENDING = 1;
	const SENT = 2;
	const ERROR = 3;
	const QUEUED = 4;
}