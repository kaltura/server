<?php
$start = microtime(true);
require_once(dirname(__FILE__).'/../../alpha/config/sfrootdir.php');

// check cache before loading anything
require_once("../lib/KalturaResponseCacher.php");
$cache = new KalturaResponseCacher();
$cache->checkOrStart(false);

require_once("../bootstrap.php");

KalturaLog::setContext("syndicationFeedRenderer");

KalturaLog::debug(">------------------------------------- syndicationFeedRenderer -------------------------------------");
KalturaLog::info("syndicationFeedRenderer-start ");

$feedId = $_GET['feedId'];
try
{
	$syndicationFeedRenderer = new KalturaSyndicationFeedRenderer($feedId);
	$syndicationFeedRenderer->addFlavorParamsAttachedFilter();
	$syndicationFeedRenderer->execute();
}
catch(Exception $ex)
{
	header('KalturaSyndication: '.$ex->getMessage());
	die;
}

$end = microtime(true);
KalturaLog::info("syndicationFeedRenderer-end [".($end - $start)."]");
KalturaLog::debug("<------------------------------------- syndicationFeedRenderer -------------------------------------");

$cache->end();

?>