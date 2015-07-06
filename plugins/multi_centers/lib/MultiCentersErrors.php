<?php
/**
 * @package plugins.multiCenters
 * @subpackage errors
 */
class MultiCentersErrors extends KalturaErrors
{
	const GET_MAX_FILESYNC_ID_FAILED = "GET_MAX_FILESYNC_ID_FAILED;DC;Failed to get max file sync id for dc \"@DC@\"";
	
	const GET_LOCK_CACHE_FAILED = "GET_LOCK_CACHE_FAILED;;Failed to get cache for locking file syncs";		
}