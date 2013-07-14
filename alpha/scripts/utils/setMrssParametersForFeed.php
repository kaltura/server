<?php

/*
 * Before running the script please insert the following two parameters
 */

//-------------------------------------------------------------------------------------------------------------
$syndicationFeedId = null; 		// TODO: insert valid syndication feed Id
$metadataFieldNames = array(
	'FIRST_FIELD', 				//TODO: change to first metadata field name
	'SECOND_FIELD', 			//TODO: change to second metadata field name
);

//-------------------------------------------------------------------------------------------------------------


require_once(dirname(__FILE__).'/../bootstrap.php');
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/scripts/' . basename(__FILE__) . '.cache');
KAutoloader::register();

// don't add to database if one of the parameters is missing or is an empty string
if (!$syndicationFeedId) {
	die ('ERROR - Missing syndication feed id');
}

$syndicationFeed = syndicationFeedPeer::retrieveByPK($syndicationFeedId);

if(!$syndicationFeed)
{
    die("ERROR - No such syndication feed with id [$syndicationFeedId].".PHP_EOL);
}

//setting custom data fields of the syndication feed
$itemXpathsToExtend = array();
foreach($metadataFieldNames as $fieldName)
{
	$itemXpathsToExtend[] = "/*[local-name()='metadata']/*[local-name()='".$fieldName."']";
}

$mrssParams = new kMrssParameters();
$mrssParams->setItemXpathsToExtend($itemXpathsToExtend);
$syndicationFeed->setMrssParameters($mrssParams);
$syndicationFeed->save();

echo "Done.";
