<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaUploadTokenStatus extends KalturaEnum
{
	/**
	 * Token created but no upload has been started yet
	 */
	const PENDING = 0;
	
	/**
	 * Upload didn't include the whole file 
	 */
	const PARTIAL_UPLOAD = 1;
	
	/**
	 * Uploaded full file
	 */
	const FULL_UPLOAD = 2;
	
	/**
	 * The entry was added
	 * @var int
	 */
	const CLOSED = 3;
	
	/**
	 * The token timed out after a certain period of time
	 */
	const TIMED_OUT = 4;
	
	/**
	 * Deleted via api
	 */
	const DELETED = 5;
}
?>