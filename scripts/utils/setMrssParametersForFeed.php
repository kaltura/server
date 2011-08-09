<?php

require_once(dirname(__FILE__).'/../bootstrap.php');

/*syndication feed Id*/
$syndicationFeedId = null;

//setting custom data fields of the syndication feed
// xpath should be of the following form: /*[local-name()='metadata']/*[local-name()='Custom_Data_Field_Name']
$itemXpathsToExtend = array();


// don't add to database if one of the parameters is missing or is an empty string
if (!$syndicationFeedId) {
	die ('Missing syndication feed id');
}

$syndicationFeed = syndicationFeedPeer::retrieveByPK($syndicationFeedId);

if(!$syndicationFeed)
{
    die("No such syndication feed with id [$syndicationFeedId].".PHP_EOL);
}


$mrssParams = new kMrssParameters();
$mrssParams->setItemXpathsToExtend($itemXpathsToExtend);
$syndicationFeed->setMrssParameters($mrssParams);
$syndicationFeed->save();

echo "Done.";
