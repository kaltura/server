<?php
/**
 * @package Core
 * @subpackage errors
 */
class kFileSyncException extends kCoreException
{
	const FILE_DOES_NOT_EXIST_ON_CURRENT_DC = 1;
	const FILE_SYNC_PARTNER_ID_NOT_DEFINED = 2;
	const FILE_SYNC_ALREADY_EXISTS = 3;
	const FILE_DOES_NOT_EXIST_ON_DISK = 4;
	const FILE_SYNC_DOES_NOT_EXIST = 5;
}