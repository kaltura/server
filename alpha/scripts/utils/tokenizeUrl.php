<?php

/**
 * This script is used to tokenize a url by a given delivery profile.
 */

require_once(dirname(__FILE__).'/../bootstrap.php');

if(count($argv) != 3) {
	die("Usage: php tokenizeUrl.php <delivery profile id> <url>");
}

$deliveryId = $argv[1];
$url = $argv[2];

$delivery = DeliveryProfilePeer::retrieveByPK($deliveryId);
if(!$delivery) 
	die("ERROR: Delivery profile id " . $deliveryId . " wasn't found");
 
$tokenizer = $delivery->getTokenizer();
if(!$tokenizer) {
	echo "Delivery profile has no tokenizer! \nUrl remains : $url";
} else {
	echo $tokenizer->tokenizeSingleUrl($url);
}

