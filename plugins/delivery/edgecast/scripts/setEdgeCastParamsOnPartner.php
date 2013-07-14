<?php

require_once(__DIR__ . '/../../../../alpha/scripts/bootstrap.php');


if($argc != 4)
{
	echo "Arguments missing.".PHP_EOL.PHP_EOL;
	echo 'Usage: php '.__FILE__.' {partner id} {edgecast account number} {api token}'.PHP_EOL;
	die();
} 

$partnerId = $argv[1];
$accountNumber = $argv[2];
$apiToken = $argv[3];

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "edgecast", "*"));
KAutoloader::setClassMapFilePath(KALTURA_ROOT_PATH.'/cache/scripts/' . basename(__FILE__) . '.cache');
KAutoloader::register();

KalturaPluginManager::addPlugin('EdgeCastPlugin');


$partner = PartnerPeer::retrieveByPK($partnerId);

if(!$partner)
{
    die("No partner with ID [$partnerId].".PHP_EOL);
}

$edgeCastParams = new kEdgeCastParams();
$edgeCastParams->setAccountNumber($accountNumber);
$edgeCastParams->setApiToken($apiToken);

EdgeCastPlugin::setEdgeCastParams($partner, $edgeCastParams);
$partner->save();

echo "Done.";
