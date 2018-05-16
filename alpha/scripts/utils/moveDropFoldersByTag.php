<?php

if($argc < 3)
	die("Usage $argv[0] <Tag> <DC from> <DC to>\r\n");

//params
$tag = $argv[1];
$dcFrom = $argv[2];
$dcTo = $argv[3];

if($dcFrom == $dcTo)
	die("<DC from> and <DC to> must contain different values\r\n");

ob_start();

chdir(dirname(__FILE__));
require_once(dirname(__FILE__) . '/../bootstrap.php');

$isCurrentDc = (kDataCenterMgr::getCurrentDcId() == $dcFrom);

//list all drop folder by tag + dc
$dropFolders = DropFolderPeer::retrieveByTag($tag,$isCurrentDc);
if(!count($dropFolders))
	die("\r\nNOTICE: Could  not find any drop folder with tag - {$tag} under dc {$dcFrom}\r\n");
foreach ($dropFolders as $dropFolder)
{
	ob_end_clean();
	echo ("\r\nINFO: Moving drop folder {$dropFolder->getId()}\r\n");
	ob_start();
	$dropFolder->setDc($dcTo);
	$dropFolder->save();
}
ob_end_clean();

$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_BATCH_JOBS);
if(!$cache)
	die("\r\nERROR: Cache layer [" . kCacheManager::CACHE_TYPE_BATCH_JOBS . "] not found, drop folder will not be allocated\r\n");
$tagKey = "drop_folder_list_key_".$tag;

if($isCurrentDc)
{
	if (!$cache->delete($tagKey))
		die ("\r\nERROR: Could not delete cache key {$tagKey} from cache layer ".kCacheManager::CACHE_TYPE_BATCH_JOBS." must do it manually!\r\n ". print_r($cache,true));
	else
		echo ("\r\nINFO: cache key {$tagKey} was deleted successfully \r\n");
}
else
{
	echo ("\r\nWARNING: Cache key {$tagKey} needs to deleted manually from DC-$dcFrom under the following cache - \r\n ".print_r($cache,true));
}

echo ("\r\nDone\r\n");
