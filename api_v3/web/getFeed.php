<?php

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
KalturaLog::info("syndicationFeedRenderer-start ");
KalturaLog::debug("getFeed Params [" . print_r(requestUtils::getRequestParams(), true) . "]");

kCurrentContext::$host = (isset($_SERVER["HOSTNAME"]) ? $_SERVER["HOSTNAME"] : null);
kCurrentContext::$user_ip = requestUtils::getRemoteAddress();
kCurrentContext::$ps_vesion = "ps3";

$feedId = getRequestParameter('feedId');
$entryId = getRequestParameter('entryId');
$limit = getRequestParameter('limit');
$ks = getRequestParameter('ks');

$feedProcessingKey = "feedProcessing_{$feedId}_{$entryId}_{$limit}";
if (function_exists('apc_fetch'))
{
	if (apc_fetch($feedProcessingKey))
	{
		KExternalErrors::dieError(KExternalErrors::PROCESSING_FEED_REQUEST);
	}
}

try
{
	$syndicationFeedRenderer = new KalturaSyndicationFeedRenderer($feedId, $feedProcessingKey, $ks);
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

// small feeds will have a short
if ($limit)
{
	if(kConf::hasParam("v3cache_getfeed_short_limits_array"))
		$short_limits = kConf::get("v3cache_getfeed_short_limits_array");
	else
		$short_limits = array(50 => 900 , 100 => 1800 , 200 => 3600 , 400 => 7200);

	//in KalturaSyndicationFeedRenderer - if the limit does restrict the amount of entries - the entries counter passes the limit's value by one , so it must be decreased back
	$entries_count = $syndicationFeedRenderer->getReturnedEntriesCount();
	$entries_count--;

	$cache_time_to_set = null;

	foreach ($short_limits as $num_of_nutries => $cache_time_frame)
	{
		if ($entries_count <= $num_of_nutries)
		{
			$cache_time_to_set = $cache_time_frame;
			break;
		}
	}

	if ($cache_time_to_set)
		KalturaResponseCacher::setExpiry($cache_time_to_set);
}


$expiryArr = kConf::hasMap("v3cache_getfeed_expiry") ? kConf::getMap("v3cache_getfeed_expiry") : array();
foreach($expiryArr as $item)
{
	if ($item["key"] == "partnerId" && $item["value"] == kCurrentContext::$partner_id ||
		$item["key"] == "feedId" && $item["value"] == $feedId)
	{
		KalturaResponseCacher::setExpiry($item["expiry"]);
		break;
	}
}

$end = microtime(true);
KalturaLog::info("syndicationFeedRenderer-end [".($end - $start)."] memory: ".memory_get_peak_usage(true));
KalturaLog::debug("<------------------------------------- syndicationFeedRenderer -------------------------------------");

$result = ob_get_contents();
ob_end_clean();
$cache->end($result);
