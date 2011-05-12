<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaControlCommandStatus extends KalturaEnum
{
	const PENDING = 0;
	const HANDLED = 1;
	const DONE = 2;
	const FAILED = 3;
}