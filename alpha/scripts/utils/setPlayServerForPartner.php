<?php

if($argc < 3)
{
	echo "Usage:\n";
	echo "	php " . __FILE__ . " <partnerId> <playServerHost>\n";
	exit(-1);
}

chdir(__DIR__);
require_once (__DIR__ . '/../bootstrap.php');

$partnerId = $argv[1];
$playServerHost = $argv[2];

$partner = PartnerPeer::retrieveByPK($partnerId);
$partner->setPlayServerHost($playServerHost);
$partner->save();
