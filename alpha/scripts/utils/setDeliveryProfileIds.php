<?php

/**
 * This script is used to set the delivery profile ids on either a given partner or a given storage profile.
 */

require_once(dirname(__FILE__).'/../bootstrap.php');

/**
 * Parameters 
 * -------------- 
 */

$partnerId = 100;
$storageId = null;

/* Delivery Ids
 * If a partner / storage profile supports a specific delivery id, it should be defined in this array.
 * The delivery Ids format is as follows:
 * 		<key> = PlaybackProtocol
 * 		<value> = array of supported delivery IDS.
 * 
 * For example :
 * 	array("http"=>array(202,203), "applehttp"=>292);.
 * */
$deliveryIds = array("http"=>array(202,203), "applehttp"=>292);

// don't add to database if one of the parameters is missing or is an empty string
if ((!$partnerId && !$storageId) || (!$deliveryIds) )
{
	die ('Missing parameter');
}

if($partnerId) {

	$partner = PartnerPeer::retrieveByPK($partnerId);
	if(!$partner)
	    die("No such partner with id [$partnerId].".PHP_EOL);

	$partner->setDeliveryIds($deliveryIds);
	$partner->save();
}	

if($storageId) {
	$storage = StorageProfilePeer::retrieveByPK($storageId);
	if(!$storageId)
		die("No such storage profile with id [$storageId].".PHP_EOL);
	
	$storage->setDeliveryIds($deliveryIds);
	$storage->save();
}

echo "Done.";

