<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaAssetStatus extends KalturaEnum
{
	const ERROR = -1;
	const QUEUED = 0;
	const READY = 2;
	const DELETED = 3;
	const IMPORTING = 7;
	const EXPORTING = 9;
}
