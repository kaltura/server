<?php
/**
 * @package plugins.multiCenters
 * @subpackage errors
 */
class MultiCentersErrors extends KalturaErrors
{
	const GET_MAX_FILESYNC_ID_FAILED = "GET_MAX_FILESYNC_ID_FAILED;DC;Failed to get max file sync id for dc \"@DC@\"";
	
	const GET_LOCK_CACHE_FAILED = "GET_LOCK_CACHE_FAILED;;Failed to get cache for locking file syncs";
	
	const GET_KEYS_CACHE_FAILED = "GET_KEYS_CACHE_FAILED;;Failed to get cache for storing file sync import status";
	
	const EXTEND_FILESYNC_LOCK_FAILED = "EXTEND_FILESYNC_LOCK_FAILED;;Failed to extend the file sync lock";
}