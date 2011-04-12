<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaBatchJobAppErrors extends KalturaEnum
{
	
	const OUTPUT_FILE_DOESNT_EXIST = 11;
	const OUTPUT_FILE_WRONG_SIZE = 12;
	const CANNOT_CREATE_DIRECTORY = 13;
	
	const NFS_FILE_DOESNT_EXIST = 21;
	
	const EXTRACT_MEDIA_FAILED = 31;
	
	const CLOSER_TIMEOUT = 41;
	
	const ENGINE_NOT_FOUND = 51;
	
	const REMOTE_FILE_NOT_FOUND = 61;
	const REMOTE_DOWNLOAD_FAILED = 62;
	
	const CSV_FILE_NOT_FOUND = 71;
	
	const CONVERSION_FAILED = 81;
	
	const THUMBNAIL_NOT_CREATED = 91;
	
	//Bulk upload exceptions
	const BULK_VALIDATION_FAILED = 101;
	const BULK_PARSE_ITEMS_FAILED = 102;
	const BULK_FILE_NOT_FOUND = 103;
	const BULK_UNKNOWN_ERROR = 104;
	const BULK_INVLAID_BULK_REQUEST_COUNT = 105;
	const BULK_ENGINE_NOT_FOUND = 106;
}
