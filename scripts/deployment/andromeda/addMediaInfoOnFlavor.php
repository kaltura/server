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

$flavor_asset_ids = @$argv[1];

$assets = explode(',', $flavor_asset_ids);

if(!count($assets))
	exit(1);
	
foreach($assets as $assetId)
{
	// find mediaInfo by flavorAssetID
	$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($assetId);
	echo 'loading asset: '.$assetId.PHP_EOL;
	$asset = flavorAssetPeer::retrieveById($assetId);
	$full_path = kFileSyncUtils::getReadyLocalFilePathForKey($asset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET));
	
	// update existing - do nothing
	if($mediaInfo)
	{
		echo 'MediaInfo for asset ['.$assetId.'] exists [id: '.$mediaInfo->getId().']. skipping...'.PHP_EOL;		
	}
	else // create if doesn't exist
	{
		echo 'creating mediainfo for asset ['.$assetId.']'.PHP_EOL;
		$mediaInfo = new mediaInfo();
		$mediaInfoParser = new KMediaInfoMediaParser($full_path);
		$KalturaMediaInfo = new KalturaMediaInfo();
		$KalturaMediaInfo = $mediaInfoParser->getMediaInfo();
		$mediaInfo = $KalturaMediaInfo->toInsertableObject($mediaInfo);
		$mediaInfo->setFlavorAssetId($asset->getId());
		$mediaInfo->save();
		KDLWrap::ConvertMediainfoCdl2FlavorAsset($mediaInfo, $asset);
		$asset->save();
		echo 'created mediainfo for asset ['.$assetId.'] id: '.$mediaInfo->getId().PHP_EOL;
	}
}
