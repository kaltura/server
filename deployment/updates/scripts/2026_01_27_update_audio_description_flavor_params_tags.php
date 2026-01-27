<?php

require_once (__DIR__ . '/../../bootstrap.php');

define("DEBUG", "debug");
define("EXECUTE", "execute");

//Debug mode iis set for testing only will be removed for final version
if(count($argv) < 2)
	die("Usage: 2026_01_27_update_AD_flavor_params_tags.php @execution_mode(debug|execute)@\n");

$executionMode = $argv[1];
if(!in_array($executionMode, array("debug", "execute")))
	die("Usage: 2026_01_27_update_AD_flavor_params_tags.php @execution_mode(debug|execute)@, invalid execution mode\n");

function getAudioDescriptionFlavorParams()
{
	$c = new Criteria();
	$c->add(assetParamsPeer::PARTNER_ID, 0);
	$c->add(assetParamsPeer::IS_DEFAULT, 1);
	$c->add(assetParamsPeer::TYPE, assetType::FLAVOR);
	$c->add(assetParamsPeer::TAGS, "%" . "audio_description" . "%", Criteria::LIKE);

	return assetParamsPeer::doSelect($c);
}

$audioDescriptionFlavorParams = getAudioDescriptionFlavorParams();

foreach ($audioDescriptionFlavorParams as $flavorParam)
{
	KalturaLog::debug("Working on asst param with id [{$flavorParam->getId()}]");

	/* @var $flavorParam flavorParams */
	$currentTags = $flavorParam->getTagsArray();
	KalturaLog::debug("Current asset tags are: " . print_r($currentTags, true));
	$currentTags[] = "dash";
	$currentTags = array_unique($currentTags);
	KalturaLog::debug("New asset tags are: " . print_r($currentTags, true));

	if($executionMode == DEBUG)
		continue;

	$flavorParam->setTags(implode(",", $currentTags));
	$flavorParam->save();
}


KalturaLog::debug("Done flavor params update");
