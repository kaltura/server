<?php

function setCacheExpiry($entriesCount , $feedId)
{
	$expiryArr = kConf::hasMap("v3cache_getfeed_expiry") ? kConf::getMap("v3cache_getfeed_expiry") : array();
	foreach($expiryArr as $item)
	{
		if ($item["key"] == "partnerId" && $item["value"] == kCurrentContext::$partner_id ||
			$item["key"] == "feedId" && $item["value"] == $feedId)
		{
			KalturaResponseCacher::setExpiry($item["expiry"]);
			return;
		}
	}

	$expiry = kConf::get("v3cache_getfeed_default_cache_time_frame" , 'local' , 86400);

	if(kConf::hasParam("v3cache_getfeed_short_limits_array"))
		$shortLimits = kConf::get("v3cache_getfeed_short_limits_array");
	else
		$shortLimits = array(50 => 900 , 100 => 1800 , 200 => 3600 , 400 => 7200);

	foreach ($shortLimits as $numOfEntries => $cacheTimeFrame)
	{
		if ($entriesCount <= $numOfEntries)
		$expiry = min($expiry , $cacheTimeFrame);
	}

	KalturaResponseCacher::setExpiry($expiry);
}

function getRequestParameter($paramName)
{
	if (isset($_GET[$paramName]))
		return $_GET[$paramName];

	// try lowercase
	$paramName = strtolower($paramName);
	if (isset($_GET[$paramName]))
		return $_GET[$paramName];
	
	return null;
}

require_once(__DIR__ . "/../bootstrap.php");

if(!getRequestParameter('feedId'))
	KExternalErrors::dieError(KExternalErrors::INVALID_FEED_ID, 'feedId not supplied');
	
ini_set( "memory_limit" , "256M" );
$start = microtime(true);
set_time_limit(0);

// check cache before loading anything
require_once(__DIR__ . "/../lib/KalturaResponseCacher.php");
$expiry = kConf::hasParam("v3cache_getfeed_default_expiry") ? kConf::get("v3cache_getfeed_default_expiry") : 86400;
$cache = new KalturaResponseCacher(null, kCacheManager::CACHE_TYPE_API_V3_FEED, $expiry);
$cache->checkOrStart();
ob_start();

// Database
DbManager::setConfig(kConf::getDB());
DbManager::initialize();

KalturaLog::debug(">------------------------------------- syndicationFeedRenderer -------------------------------------");
KalturaLog::debug("getFeed Params [" . print_r(requestUtils::getRequestParams(), true) . "]");

kCurrentContext::$host = (isset($_SERVER["HOSTNAME"]) ? $_SERVER["HOSTNAME"] : null);
kCurrentContext::$user_ip = requestUtils::getRemoteAddress();
kCurrentContext::$ps_vesion = "ps3";

$feedId = getRequestParameter('feedId');
$entryId = getRequestParameter('entryId');
$limit = getRequestParameter('limit');
$ks = getRequestParameter('ks');
$state = getRequestParameter('state');

$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_LOCK);
$feedProcessingKey = "feedProcessing_{$feedId}_{$entryId}_{$limit}";
if($cache && $cache->get($feedProcessingKey))
{
	KExternalErrors::dieError(KExternalErrors::PROCESSING_FEED_REQUEST);
}

try
{
	$syndicationFeedRenderer = new KalturaSyndicationFeedRenderer($feedId, $feedProcessingKey, $ks, $state);
	$syndicationFeedRenderer->addFlavorParamsAttachedFilter();
	
	kCurrentContext::$partner_id = $syndicationFeedRenderer->syndicationFeed->partnerId;
	
	if (isset($entryId))
		$syndicationFeedRenderer->addEntryAttachedFilter($entryId);
		
	$syndicationFeedRenderer->execute($limit);
}
catch(PropelException $pex)
{
	KalturaLog::alert($pex->getMessage());
	KExternalErrors::dieError(KExternalErrors::PROCESSING_FEED_REQUEST, 'KalturaSyndication: Database error');
}
catch(Exception $ex)
{
	KalturaLog::err($ex->getMessage());
	$msg = 'KalturaSyndication: ' . str_replace(array("\n", "\r"), array("\t", ''), $ex->getMessage());
	KExternalErrors::dieError(KExternalErrors::PROCESSING_FEED_REQUEST, $msg);
}

//in KalturaSyndicationFeedRenderer - if the limit does restrict the amount of entries - the entries counter passes the limit's value by one , so it must be decreased back
$entriesCount = $syndicationFeedRenderer->getReturnedEntriesCount();
$entriesCount--;

setCacheExpiry($entriesCount , $feedId);

$end = microtime(true);
KalturaLog::info("syndicationFeedRenderer-end [".($end - $start)."] memory: ".memory_get_peak_usage(true));
KalturaLog::debug("<------------------------------------- syndicationFeedRenderer -------------------------------------");

$result = ob_get_contents();
ob_end_clean();
$cache->end($result);
