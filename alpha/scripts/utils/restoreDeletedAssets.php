<?php
ini_set('memory_limit','1024M');
if ($argc < 3){
	die ($argv[0]. " <Partner ID> <comma,separated,asset,Ids> <dry-run|real-run>.\n");
}

$partnerId = $argv[1];
$entryIds = $argv[2];
$entryIdsArray = explode(',', trim($entryIds));
require_once(__DIR__ . '/../bootstrap.php');
KalturaStatement::setDryRun($argv[3] !== 'real-run');

if (!PartnerPeer::retrieveByPK($partnerId)){
	die ("Partner ID not found.\n");
}

$c = new Criteria();
$c->add(assetPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
$c->add(assetPeer::STATUS, entryStatus::DELETED, Criteria::EQUAL);
$c->add(assetPeer::ID, $entryIdsArray, Criteria::IN);
assetPeer::setUseCriteriaFilter(false);
$assets = assetPeer::doSelect($c);
foreach($assets as $deletedAsset){
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
			if (file_exists($file_full_path)){
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
			if (file_exists($file_full_path)){
				$assetConvertLogfileSync->setStatus (FileSync::FILE_SYNC_STATUS_READY);
				$assetConvertLogfileSync->save();
			}else{
				echo "LOG: will not revive file sync as $file_full_path does not exist on disk.\n";
			}
		}
	}
}
