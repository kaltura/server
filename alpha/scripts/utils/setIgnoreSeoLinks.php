<?php

chdir(dirname(__FILE__));
require_once(dirname(__FILE__) . '/../bootstrap.php');

$partnerId = null;
if ( $argc == 3)
{	
	$partnerId = (int) $argv[1];
	$ignore = (int) $argv[2];
}

if(!$partnerId)
{
	die ( 'usage: php ' . $_SERVER ['SCRIPT_NAME'] . " [partner id] [ignore 0/1]" . PHP_EOL );
}

$partner = PartnerPeer::retrieveByPK($partnerId);
$partner->setIgnoreSeoLinks($ignore);
$partner->save();

echo "Done.";
