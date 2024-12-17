<?php
ini_set('memory_limit','1024M');
if ($argc < 3){
	die ($argv[0]. " <Partner ID> <assets file> <dry-run|real-run>.\n");
}

$partnerId = $argv[1];
$assetIdsFile = $argv[2];
$assetIdsArray = file($assetIdsFile, FILE_IGNORE_NEW_LINES);
require_once(__DIR__ . '/../bootstrap.php');
KalturaStatement::setDryRun($argv[3] !== 'real-run');

if (!PartnerPeer::retrieveByPK($partnerId)){
	die ("Partner ID not found.\n");
}
echo ('LOG:: processing ' . count($assetIdsArray) . ' assets.' . PHP_EOL);

$chunks = array_chunk($assetIdsArray, 100);

foreach ($chunks as $assetIdsChunk)
{
	echo ('LOG:: processing asset IDs: ' . implode(',', $assetIdsChunk) . PHP_EOL);
	$c = new Criteria();
	$c->add(assetPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
	$c->add(assetPeer::ID, $assetIdsChunk, Criteria::IN);
	assetPeer::setUseCriteriaFilter(false);
	$assets = assetPeer::doSelect($c);
	foreach($assets as $processedAsset){
		/* @var $processedAsset flavorAsset */
		$currentVersion = $processedAsset->getVersion();
		echo('LOG: Resetting asset '. $processedAsset->getId(). " from version $currentVersion to the previous version \n");

		$previousVersion = $currentVersion - 10;
		FileSyncPeer::setUseCriteriaFilter(false);
		$assetSyncKey = $processedAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET, $previousVersion);
		$assetfileSyncs = FileSyncPeer::retrieveAllByFileSyncKey($assetSyncKey);
		if (!count($assetfileSyncs)){
			echo('LOG: Cannot reset asset '. $processedAsset->getId(). " To version $previousVersion: file syncs for version not found!! \n");
			continue;
		}

		foreach ($assetfileSyncs as $assetfileSync) {
			if ($assetfileSync->getStatus () == FileSync::FILE_SYNC_STATUS_DELETED || $assetfileSync->getStatus () == FileSync::FILE_SYNC_STATUS_PURGED) {
				/* @var $assetfileSync FileSync */
				if (kFileSyncUtils::file_exists(kFileSyncUtils::getKeyForFileSync($assetfileSync))){
					echo('LOG: Changing status of file_sync '. $assetfileSync->getId().' to: '. FileSync::FILE_SYNC_STATUS_READY.".\n");
					$assetfileSync->setStatus (FileSync::FILE_SYNC_STATUS_READY);
					$assetfileSync->save();
				}else{
					echo "LOG: will not revive file sync {$assetfileSync->getId()} as content for {$processedAsset->getId()} does was probably purged.\n";
					continue;
				}
			}
		}

		$processedAsset->setVersion($previousVersion);
		$processedAsset->save();
		assetPeer::clearInstancePool();
		kMemoryManager::clearMemory();
	}
}

