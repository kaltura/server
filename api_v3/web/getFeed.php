<?php
if(!isset($_GET['feedId']))
	die('feedId not supplied');
	
ini_set( "memory_limit" , "256M" );
$start = microtime(true);
set_time_limit(0);
require_once(dirname(__FILE__).'/../../alpha/config/sfrootdir.php');

// check cache before loading anything
require_once("../lib/KalturaResponseCacher.php");
require_once(dirname(__FILE__) . '/../../infra/cache/kCacheManager.php');
$expiry = kConf::hasParam("v3cache_getfeed_default_expiry") ? kConf::get("v3cache_getfeed_default_expiry") : 86400;
$cache = new KalturaResponseCacher(null, kCacheManager::FS_API_V3_FEED, $expiry);
$cache->checkOrStart();

require_once("../bootstrap.php");

KalturaLog::setContext("syndicationFeedRenderer");

KalturaLog::debug(">------------------------------------- syndicationFeedRenderer -------------------------------------");
KalturaLog::info("syndicationFeedRenderer-start ");
KalturaLog::debug("getFeed Params [" . print_r(requestUtils::getRequestParams(), true) . "]");

kCurrentContext::$host = (isset($_SERVER["HOSTNAME"]) ? $_SERVER["HOSTNAME"] : null);
kCurrentContext::$user_ip = requestUtils::getRemoteAddress();
kCurrentContext::$ps_vesion = "ps3";

$feedId = $_GET['feedId'];
$entryId = (isset($_GET['entryId']) ? $_GET['entryId'] : null);
$limit = (isset($_GET['limit']) ? $_GET['limit'] : null);
try
{
	$syndicationFeedRenderer = new KalturaSyndicationFeedRenderer($feedId);
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
KalturaLog::info("syndicationFeedRenderer-end [".($end - $start)."]");
KalturaLog::debug("<------------------------------------- syndicationFeedRenderer -------------------------------------");

$cache->end();

?>
