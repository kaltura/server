<?php
/**
 * @package plugins.dropFolder
 * @subpackage model.enum
 */ 
interface DropFolderFileStatus extends BaseEnum
{
	const UPLOADING         = 1;  // file is still being uploaded to the drop folder
	const PENDING           = 2;  // pending for entry association
	const WAITING           = 3;  // waiting for more files to upload to the drop folder	
	const HANDLED           = 4;  // file handling finished
	const IGNORE            = 5;  // don't show in unmatched file list
	const DELETED           = 6;  // file is marked as deleted – batch will later delete the file and change status to PURGED
	const PURGED            = 7;  // file is physically deleted
	const NO_MATCH          = 8;  // no match found for the file
	const ERROR_HANDLING    = 9;  // error - file handling cannot continue
	const ERROR_DELETING	= 10; // error occured while trying to delete the file
	const DOWNLOADING       = 11; // file is being downloaded to the local storage
	const ERROR_DOWNLOADING = 12; // error while downloading file to local storage
	const PROCESSING        = 13; //in process by batch job
	const PARSED			= 14; //XML content resource parsed from the XML but wasn't added yet to the drop folder
	const DETECTED			= 15; //Resource was detected by FTP or Aspera servers as started upload
}