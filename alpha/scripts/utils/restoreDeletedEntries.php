<?php
ini_set("memory_limit","1024M");

if (count($argv) == 1)
{
	die ('Partner ID required.\n');
}

$partnerId = $argv[1];

require_once(__DIR__ . '/../bootstrap.php');

if (!PartnerPeer::retrieveByPK($partnerId))
{
	die ('Partner ID not found.');
}

$c = new Criteria();
$c->add(entryPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
$c->add(entryPeer::STATUS, entryStatus::DELETED, Criteria::EQUAL);
BaseentryPeer::setUseCriteriaFilter(false);
$entries = entryPeer::doSelect($c);

foreach ($entries as $deletedEntry)
{
	/* @var $deletedEntry entry */
	echo('changing status of entry '. $deletedEntry->getId());
	$deletedEntry->setStatusReady();
	$deletedEntry->save();
	entryPeer::clearInstancePool();
	
	$entryFileSyncKey = $deletedEntry->getSyncKey(kEntryFileSyncSubType::THUMB);
	
	$entryFileSyncs = FileSyncPeer::retrieveAllByFileSyncKey($entryFileSyncKey);
	foreach ($entryFileSyncs as $entryFileSync){
		$entryFileSync->setStatus ( FileSync::FILE_SYNC_STATUS_READY );
		$entryFileSync->save();
	}
	
	//Restore assets
	$assetCrit = new Criteria();
	$assetCrit->add(assetPeer::ENTRY_ID, $deletedEntry->getId(), Criteria::EQUAL);
	$assetCrit->add(assetPeer::STATUS, asset::ASSET_STATUS_DELETED, Criteria::EQUAL);
	assetPeer::setUseCriteriaFilter(false);
	$deletedAssets = assetPeer::doSelect($assetCrit);
	
	foreach($deletedAssets as $deletedAsset)
	{
		/* @var $deletedAsset asset */
		echo('changing status of asset '. $deletedAsset->getId());
		$deletedAsset->setStatus(asset::ASSET_STATUS_READY);
		$deletedAsset->save();
		assetPeer::clearInstancePool();
		
		$assetSyncKey = $deletedAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		$assetfileSyncs = FileSyncPeer::retrieveAllByFileSyncKey($assetSyncKey);
		foreach ( $assetfileSyncs as $assetfileSync ) 
		{
			if ($assetfileSync->getStatus () == FileSync::FILE_SYNC_STATUS_DELETED) 
			{
			    $file_full_path=$assetfileSync->getFullPath();
			    if (file_exists($file_full_path)){
				    echo('LOG: Changing status of file_sync '. $assetfileSync->getId().' to: '. FileSync::FILE_SYNC_STATUS_READY.".\n");
				    $assetfileSync->setStatus (FileSync::FILE_SYNC_STATUS_READY);
				    $assetfileSync->save ();
			    }else{
				    echo "LOG: will not revive file sync as $file_full_path does not exist on disk.\n";
			    }
			}
		}
		
		//restore asset's convert-log's file syncs.
		$assetConvertLogSyncKey = $deletedAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_CONVERT_LOG);
		$assetConvertLogfileSyncs = FileSyncPeer::retrieveAllByFileSyncKey($assetConvertLogSyncKey);
		foreach ( $assetConvertLogfileSyncs as $assetConvertLogfileSync ) 
		{
			if ($assetConvertLogfileSync->getStatus () == FileSync::FILE_SYNC_STATUS_DELETED) 
			{
			    $file_full_path=$assetConvertLogfileSync->getFullPath();
			    if (file_exists($file_full_path)){
				    $assetConvertLogfileSync->setStatus (FileSync::FILE_SYNC_STATUS_READY);
				    $assetConvertLogfileSync->save ();
			    }else{
				    echo "LOG: will not revive file sync as $file_full_path does not exist on disk.\n";
			    }
			}
		}
		
		
	}
}
