<?php
/**
 * This script lists all distribution profiles and check if they have a valid authentication info
 */


require_once(__DIR__.'/../bootstrap.php');

if (count($argv) < 2)
{
	die ("Partner id is required input.\n");
}

$partnerId = intval($argv[1]);
if($partnerId <= 0)
{
	die ("Partner id must be a real partner id.\n");
}

$partner = PartnerPeer::retrieveByPK($partnerId);
$partner->removeFromCustomData(null, 'googleAuth');
$partner->save();
