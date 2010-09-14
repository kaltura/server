<?php

require_once ( "./define.php" );

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'lib/myContentStorage.class.php');
require_once(SF_ROOT_DIR.'/../infra/bootstrap_base.php');
define("KALTURA_API_PATH", KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR."api_v3");

require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "api_v3", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "batch", "mediaInfoParser", "*"));
KAutoloader::setClassMapFilePath('./logs/classMap.cache');
KAutoloader::register();

error_reporting(E_ALL);

ini_set("memory_limit","256M");

$databaseManager = new sfDatabaseManager();
$databaseManager->initialize();

$start_int_id = @$argv[1];
$stop_int_id = @$argv[2];
$partner_id = @$argv[3];
$starting_date = @$argv[4];

$c = new Criteria();
$c->addAnd(flavorAssetPeer::TAGS, '%edit%', Criteria::NOT_LIKE);
$c->addAnd(flavorAssetPeer::INT_ID, $start_int_id, Criteria::GREATER_EQUAL);
$c->addAnd(flavorAssetPeer::INT_ID, $stop_int_id, Criteria::LESS_THAN);
if($partner_id)
{
	$c->addAnd(flavorAssetPeer::PARTNER_ID, $partner_id);
}
if($starting_date)
{
	$c->addAnd(flavorAssetPeer::CREATED_AT, $starting_date, Criteria::GREATER_EQUAL);
}

$assets = flavorAssetPeer::doSelect($c);

$count = 0;
$assetIds = array();
$lastStartCount = 0;
foreach($assets as $asset)
{
	$count++;
	$assetIds[] = $asset->getId();
	
	if(count($assetIds) == 200)
	{
		echo "sending 200 assets, starting $lastStartCount to $count".PHP_EOL;
		$strAssetIds = implode(',', $assetIds);
		$output = array();
		$command = 'php addMediaInfoOnFlavor.php "'.$strAssetIds.'"';
		
		exec($command, $output, $error);
		$assetIds = array();
		$strAssetIds = '';
		$lastStartCount = $count+1;
	}
}
// one last time:
if(count($assetIds))
{
	echo "sending ".($count-$lastStartCount)." assets, starting $lastStartCount to $count".PHP_EOL;
	$strAssetIds = implode(',', $assetIds);
	$output = array();
	$command = 'php addMediaInfoOnFlavor.php "'.$strAssetIds.'"';
	
	exec($command, $output, $error);
	$assetIds = array();
	$strAssetIds = '';
	$lastStartCount = $count+1;	
}