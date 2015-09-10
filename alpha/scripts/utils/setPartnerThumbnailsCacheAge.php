<?php

require_once(dirname(__FILE__).'/../bootstrap.php');

if (count($argv) < 3) die ("\nUsage : php $argv[0] 2 values required: \nPartner ID \nCache Age (int) \n");

$partnerId = $argv[1];
$cacheAge  = $argv[2];

if(!ctype_digit($cacheAge))
    die("Cache age must be an integer.".PHP_EOL);

$partner = PartnerPeer::retrieveByPK($partnerId);
if(!$partner)
    die("No such partner with id [$partnerId].".PHP_EOL);


$partner->setThumbnailCacheAge((int)$cacheAge);
$partner->save();

echo "Done.".PHP_EOL;