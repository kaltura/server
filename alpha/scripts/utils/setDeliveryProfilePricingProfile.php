<?php

/**
 * This script is used to set the pricing profile for delivery profile ids given via stdin
*/

require_once(dirname(__FILE__).'/../bootstrap.php');

if(count($argv) != 2) {
	die("Usage: php setDeliveryProfilePricingProfile.php pricingProfile");
}

$pricingProfile = $argv[1];

$f = fopen("php://stdin", "r");
while(!feof($f))
{
	$s = trim(fgets($f));
	$dp = DeliveryProfilePeer::retrieveByPk($s);
	if ($dp)
	{
		$dp->setPricingProfile($pricingProfile);
		$dp->save();
	}
}
