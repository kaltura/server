<?php
/**
 * @package plugins.dropFolder
 * @subpackage model.enum
 */ 
interface DropFolderFileStatus extends BaseEnum
{
	const UPLOADING         = 1; // file is still being uploaded to the drop folder
	const PENDING           = 2; // pending for entry association
	const WAITING           = 3; // waiting for more files to upload to the drop folder	
	const HANDLED           = 4; // file handling finished
	const IGNORE            = 5; // don't show in unmatched file list
	const DELETED           = 6; // file is deleted
	const NO_MATCH          = 7; // no match found for the file
	const ERROR_HANDLING    = 8; // error - file handling cannot continue
	
	// future remote drop folder status that might be in use
	
	// const DOWNLOADING
	// const ERROR_DOWNLOADING
	// const DELETING	
	// const ERROR_DELETING	
}