<?php

require_once (__DIR__ . '/../../bootstrap.php');

define("DEBUG", "debug");
define("EXECUTE", "execute");

//Debug mode iis set for testing only will be removed for final version
if(count($argv) < 2)
	die("Usage: 2016_10_30_update_live_flavor_tags.php @execution_mode(debug|execute)@\n");

$executionMode = $argv[1];
if(!in_array($executionMode, array("debug", "execute")))
	die("Usage: 2016_10_30_update_live_flavor_tags.php @execution_mode(debug|execute)@, invalid execution mode\n");

function getIngestLiveFlavorParams()
{
	$c = new Criteria();
	$c->add(assetParamsPeer::PARTNER_ID, 0);
	$c->add(assetParamsPeer::IS_DEFAULT, 1);
	$c->add(assetParamsPeer::TYPE, assetType::LIVE);
	$c->add(assetParamsPeer::TAGS, "ingest", Criteria::LIKE);
	
	return assetParamsPeer::doSelect($c);
}

$liveIngestParams = getIngestLiveFlavorParams();

foreach ($liveIngestParams as $ingestParam)
{
	KalturaLog::debug("Working on asst param with id [{$ingestParam->getId()}]");
	
	/* @var $ingestParam liveParams */
	$currentTags = $ingestParam->getTagsArray();
	KalturaLog::debug("Current assets tag are: " . print_r($currentTags, true));
	$currentTags[] = "ipadnew";
	$currentTags[] = "iphonenew";
	$currentTags = array_unique($currentTags);
	KalturaLog::debug("New assets tag are: " . print_r($currentTags, true));
	
	if($executionMode == DEBUG)
		continue;
	
	$ingestParam->setTags(implode(",", $currentTags));
	$ingestParam->save();
}


KalturaLog::debug("Done flavor params update");
