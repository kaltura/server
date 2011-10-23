<?php
ini_set("memory_limit","1024M");

if (count($argv) == 1)
{
	die ('Partner ID required.\n');
}

$partnerId = $argv[1];

require_once 'bootstrap.php';
$dbConf = kConf::getDB();
DbManager::setConfig ( $dbConf );
DbManager::initialize ();

if (!PartnerPeer::retrieveByPK($partnerId))
{
	die ('Partner ID not found.');
}

$c = new Criteria();
$c->add(entryPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
$c->add(entryPeer::STATUS, entryStatus::DELETED, Criteria::EQUAL);
BaseentryPeer::setDefaultCriteriaFilter();
$entries = entryPeer::doSelect($c);

foreach ($entries as $deletedEntry)
{
	/* @var $deletedEntry entry */
	echo('changing status of entry '. $deletedEntry->getId());
	$deletedEntry->setStatusReady();
	$deletedEntry->save();
	entryPeer::clearInstancePool();
	
	$entryFileSyncKey = $deletedEntry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB);
	
	$entryFileSync = FileSyncPeer::retrieveByFileSyncKey($entryFileSyncKey);
	
	$entryFileSync->setStatus(FileSync::FILE_SYNC_STATUS_READY);
	
	$entryFileSync->save();
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
		$assetfileSync = FileSyncPeer::retrieveByFileSyncKey($assetSyncKey);
		if ($assetfileSync->getStatus() == FileSync::FILE_SYNC_STATUS_DELETED)
		{
			$assetfileSync->setStatus(FileSync::FILE_SYNC_STATUS_READY);
		}
		$assetfileSync->save();
	}
}
