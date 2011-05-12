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
	
	//Bulk upload exceptions
	const BULK_FILE_NOT_FOUND = 71;
	const BULK_VALIDATION_FAILED = 72;
	const BULK_PARSE_ITEMS_FAILED = 73;
	const BULK_UNKNOWN_ERROR = 74;
	const BULK_INVLAID_BULK_REQUEST_COUNT = 75;
	const BULK_NO_ENRIES_CREATED = 76;
	const BULK_ACTION_NOT_SUPPORTED = 77;
	const BULK_MISSING_MANDATORY_PARAMETER = 78;
	const BULK_ITEM_VALIDATION_FAILED = 79;
		
	const CONVERSION_FAILED = 81;
	
	const THUMBNAIL_NOT_CREATED = 91;
	
}
