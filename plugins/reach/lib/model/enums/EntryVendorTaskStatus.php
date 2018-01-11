<?php
/**
 * @package plugins.reach
 * @subpackage model.enum
 */ 
interface EntryVendorTaskStatus extends BaseEnum
{
	const PENDING 				= 1;
	const READY 				= 2;
	const PROCESSING 			= 3;
	const PENDING_MODERATION 	= 4;
	const REJECTED 				= 5;
	const ERROR 				= 6;
	const ABORTED 				= 7;
}