<?php

if($argc < 3)
{
	die("Usage $argv[0] <TAGS_FILE_PATH> <DC from> <DC to>\r\n");
}

$tagsFilePath = $argv[1];
$dcFrom = $argv[2];
$dcTo = $argv[3];

if($dcFrom == $dcTo)
{
	die("<DC from> and <DC to> must contain different values\r\n");
}

require_once(dirname(__FILE__) . '/../bootstrap.php');

$isCurrentDc = (kDataCenterMgr::getCurrentDcId() == $dcFrom);

$tags = file ($tagsFilePath) or die ('Could not read tags file'."\n");
foreach ($tags as $tag)
{
	$tag = trim($tag);
	KalturaLog::debug("Start handling drop folders for tag {$tag} under dc {$dcFrom}");
	$dropFolders = DropFolderPeer::retrieveByTag($tag,$isCurrentDc);
	if(!count($dropFolders))
	{
		KalturaLog::debug("Could not find any drop folder with tag - {$tag} under dc {$dcFrom}");
	}

	foreach ($dropFolders as $dropFolder)
	{
		KalturaLog::debug("Moving drop folder {$dropFolder->getId()}");
		$dropFolder->setDc($dcTo);
		$dropFolder->save();
	}

	$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_BATCH_JOBS);
	if(!$cache)
	{
		die("\r\nERROR: Cache layer [" . kCacheManager::CACHE_TYPE_BATCH_JOBS . "] not found, drop folder will not be allocated\r\n");
	}

	$tagKey = "drop_folder_list_key_".$tag;

	if($isCurrentDc)
	{
		if (!$cache->delete($tagKey))
		{
			die ("\r\nERROR: Could not delete cache key {$tagKey} from cache layer ".kCacheManager::CACHE_TYPE_BATCH_JOBS." must do it manually!\r\n ". print_r($cache,true));
		}
		else
		{
			KalturaLog::debug("cache key {$tagKey} was deleted successfully");
		}
	}
	else
	{
		KalturaLog::warning("Cache key {$tagKey} needs to deleted manually from DC-$dcFrom under the following cache - \r\n ".print_r($cache,true));
	}
}

KalturaLog::debug("Done");