<?php
/**
 * @package plugins.dropFolder
 * @subpackage model.enum
 */ 
interface DropFolderFileStatus extends BaseEnum
{
	const UPLOADING         = 1;
	const PENDING           = 2;
	const DOWNLOADING       = 3;
	const HANDLED           = 4;
	const IGNORE            = 5;
	const DELETING          = 6;
	const DELETED           = 7;	
	const ERROR_DOWNLOADING = 8;
	const ERROR_DELETING    = 9;	
}