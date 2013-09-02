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

if(!getRequestParameter('feedId'))
	die('feedId not supplied');
	
ini_set( "memory_limit" , "256M" );
$start = microtime(true);
set_time_limit(0);

// check cache before loading anything
require_once(__DIR__ . "/../lib/KalturaResponseCacher.php");
$expiry = kConf::hasParam("v3cache_getfeed_default_expiry") ? kConf::get("v3cache_getfeed_default_expiry") : 86400;
$cache = new KalturaResponseCacher(null, kCacheManager::CACHE_TYPE_API_V3_FEED, $expiry);
$cache->checkOrStart();
ob_start();

require_once(__DIR__ . "/../bootstrap.php");

KalturaLog::setContext("syndicationFeedRenderer");

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
	header('KalturaSyndication: Database error');
	die;
}
catch(Exception $ex)
{
	KalturaLog::err($ex->getMessage());
	header('KalturaSyndication: ' . str_replace(array("\n", "\r"), array("\t", ''), $ex->getMessage()));
	die;
}

$syndicationFeedDB = syndicationFeedPeer::retrieveByPK($feedId);
if( !$syndicationFeedDB )
{
	header('KalturaSyndication: Feed Id not found');
	die;
}

// small feeds will have a short 
if ($limit)
{
	$short_limit = kConf::hasParam("v3cache_getfeed_short_limit") ? kConf::get("v3cache_getfeed_short_limit") : 50;
	if ($limit < $short_limit)
	{
		KalturaResponseCacher::setExpiry(kConf::hasParam("v3cache_getfeed_short_expiry") ? kConf::get("v3cache_getfeed_short_expiry") : 900);
	}
}

$partnerId = $syndicationFeedDB->getPartnerId();
$expiryArr = kConf::hasMap("v3cache_getfeed_expiry") ? kConf::getMap("v3cache_getfeed_expiry") : array();
foreach($expiryArr as $item)
{
	if ($item["key"] == "partnerId" && $item["value"] == $partnerId ||
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
