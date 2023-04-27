<?php
ini_set('memory_limit','1024M');
if ($argc < 3)
{
	echo PHP_EOL . ' ---- Restore Deleted Assets ---- ' . PHP_EOL;
	echo ' Execute: php ' . $argv[0] . ' [ /path/to/assets_id_list || assetId_1,assetId_2,.. || asset_id ] [partnerId] [real-run / dryrun]' . PHP_EOL;
	die(' Error: missing assets_ids or partner_id ' . PHP_EOL . PHP_EOL);
}


if (is_file($argv[1]))
{
	$assetsIds = file($argv[1]) or die (' Error: cannot open file at: "' . $argv[1] .'"' . PHP_EOL);
}
elseif (strpos($argv[1], ','))
{
	$assetsIds = explode(',', $argv[1]);
}
elseif (strpos($argv[1],'_'))
{
	$assetsIds[] = $argv[1];
}
else
{
	die (' Error: invalid input supplied at: "' . $argv[1] . '"' . PHP_EOL);
}

require_once(__DIR__ . '/../bootstrap.php');
$partnerId = $argv[2];
$dryRun = true;
if (isset($argv[3]) && $argv[3] === 'real-run')
	$dryRun = false;
KalturaStatement::setDryRun($dryRun);

if (!PartnerPeer::retrieveByPK($partnerId)){
	die ("Partner ID not found.\n");
}
	
$count = 0;
$totalAssets = count($assetsIds);
foreach ($assetsIds as $deletedAssetId)
{
	$deletedAssetId = trim($deletedAssetId);
	$c = new Criteria();
	$c->add(assetPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
	$c->add(assetPeer::STATUS, flavorAsset::ASSET_STATUS_DELETED, Criteria::EQUAL);
	$c->add(assetPeer::ID,$deletedAssetId);
	assetPeer::setUseCriteriaFilter(false);
	$assets = assetPeer::doSelect($c);
	if (count($assets) > 0)
		$deletedAsset = $assets[0];
	else
		continue;
	echo('LOG: Changing status of asset '. $deletedAsset->getId().' to: '. asset::ASSET_STATUS_READY.".\n");
	$deletedAsset->setStatus(asset::ASSET_STATUS_READY);
	$deletedAsset->save();
	assetPeer::clearInstancePool();
	FileSyncPeer::setUseCriteriaFilter(false);	
	$assetSyncKey = $deletedAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
	$assetfileSyncs = FileSyncPeer::retrieveAllByFileSyncKey($assetSyncKey);
	foreach ($assetfileSyncs as $assetfileSync) {
		if ($assetfileSync->getStatus () == FileSync::FILE_SYNC_STATUS_DELETED || $assetfileSync->getStatus () == FileSync::FILE_SYNC_STATUS_PURGED) {
			$file_full_path=$assetfileSync->getFullPath();
			if (kFile::checkFileExists($file_full_path)){
				echo('LOG: Changing status of file_sync '. $assetfileSync->getId().' to: '. FileSync::FILE_SYNC_STATUS_READY.".\n");
				$assetfileSync->setStatus (FileSync::FILE_SYNC_STATUS_READY);
				$assetfileSync->save();
			}else{
				echo "LOG: will not revive file sync as $file_full_path does not exist on disk.\n";
			}
		}
	}
	
	//restore asset's convert-log's file syncs.
	$assetConvertLogSyncKey = $deletedAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_CONVERT_LOG);
	$assetConvertLogfileSyncs = FileSyncPeer::retrieveAllByFileSyncKey($assetConvertLogSyncKey);
	foreach ($assetConvertLogfileSyncs as $assetConvertLogfileSync) {
		if ($assetConvertLogfileSync->getStatus () == FileSync::FILE_SYNC_STATUS_DELETED || $assetConvertLogfileSync->getStatus () == FileSync::FILE_SYNC_STATUS_PURGED) {
			$file_full_path=$assetConvertLogfileSync->getFullPath();
			if (kFile::checkFileExists($file_full_path)){
				$assetConvertLogfileSync->setStatus (FileSync::FILE_SYNC_STATUS_READY);
				$assetConvertLogfileSync->save();
			}else{
				echo "LOG: will not revive file sync as $file_full_path does not exist on disk.\n";
			}
		}
	}
	$count++;
	if ($count % 1000 === 0)
	{
		KalturaLog::debug('Currently at: ' . $count . ' out of: ' . $totalAssets);
		KalturaLog::debug('Sleeping for 30 seconds');
		sleep(30);
	}
}

