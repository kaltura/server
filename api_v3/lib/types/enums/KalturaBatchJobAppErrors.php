<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaBatchJobAppErrors extends KalturaEnum
{
	const OUTPUT_FILE_DOESNT_EXIST = 11;
	const OUTPUT_FILE_WRONG_SIZE = 12;
	
	const NFS_FILE_DOESNT_EXIST = 21;
	
	const EXTRACT_MEDIA_FAILED = 31;
	
	const CLOSER_TIMEOUT = 41;
	
	const ENGINE_NOT_FOUND = 51;
	
	const REMOTE_FILE_NOT_FOUND = 61;
	const REMOTE_DOWNLOAD_FAILED = 62;
	
	const CSV_FILE_NOT_FOUND = 71;
	
	const CONVERSION_FAILED = 81;
	
	const THUMBNAIL_NOT_CREATED = 91;
}
?>