<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

$partnerId = null;
$startCategoryId = null;
$page = 500;

$dryRun = true;

if($argc < 2)
	die ( 'usage: php ' . $_SERVER ['SCRIPT_NAME'] . " [partner id] [default entit scope 1 or 0]" . PHP_EOL );
	
$partnerId = $argv[1];
$dafualtEntitScope = $argv[2];
	
$partner = PartnerPeer::retrieveByPK($partnerId);
if(!$partner)
	die('Partner id not found: ' . $partnerId . PHP_EOL);

$partner->setDefaultEntitlementEnforcement($dafualtEntitScope);	
$partner->save();

KalturaLog::info("Done" . PHP_EOL);
