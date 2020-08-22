<?php

if (count($argv) != 7)
{
	echo "USAGE: <source storage ids> <target storage id> <file name> <type - entry/asset> <partner ids/all> <realrun-dryrun>\n";
	exit(0);
}

define("BASE_DIR", dirname(__FILE__));
require_once(BASE_DIR.'/../../../alpha/scripts/bootstrap.php');

$sourceDcIds = explode(',', $argv[1]);
$targetDcId = $argv[2];
$filePath = $argv[3];
$fileType = $argv[4];
$partnerIds = $argv[5] != 'all' ? explode(',', $argv[5]) : null;
$dryRun = $argv[6] != 'realrun';

if (!is_numeric($targetDcId))
{
	KalturaLog::warning("Destination storage id should be numeric");
	exit(1);
}

if (!file_exists($filePath))
{
	KalturaLog::warning("File $filePath does not exist");
	exit(1);
}

if ($dryRun)
{
	KalturaLog::debug('*************** In Dry run mode ***************');
}
else
{
	KalturaLog::debug('*************** In Real run mode ***************');
}
KalturaStatement::setDryRun($dryRun);

main($sourceDcIds, $targetDcId, $filePath, $fileType, $partnerIds);


function handleAsset($asset, $sourceDcIds, $targetStorage)
{
	$assetId = $asset->getId();

	$targetDcId = $targetStorage->getId();
	KalturaLog::debug('Handling asset ' . $assetId);

	if (!$targetStorage->shouldExportFlavorAsset($asset))
	{
		KalturaLog::info(">>> $assetId: NO_EXPORT - Asset should not be exported to target storage " . $targetDcId);
		return;
	}

	$criteria = new Criteria(FileSyncPeer::DATABASE_NAME);
	$criteria->add(FileSyncPeer::OBJECT_ID, $asset->getId(), Criteria::EQUAL);
	$criteria->add(FileSyncPeer::OBJECT_TYPE, FileSyncObjectType::ASSET);
	$criteria->add(FileSyncPeer::VERSION, $asset->getVersion());
	$criteria->add(FileSyncPeer::OBJECT_SUB_TYPE, flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
	$criteria->add(FileSyncPeer::DELETED_ID, 0, Criteria::EQUAL);
	$fileSyncs = FileSyncPeer::doSelect($criteria);

	$targetDcFileSync = null;
	$sourceDcFileSync = null;

	// Get the Local fileSync to handle
	foreach ($fileSyncs as /** @var FileSync $fileSync * */ $fileSync)
	{
		if ($fileSync->getDc() == $targetDcId)
		{
			$targetDcFileSync = $fileSync;
			continue;
		}

		if (!in_array($fileSync->getDc(), $sourceDcIds) || !$fileSync->getFileSize())
		{
			continue;
		}

		if ($sourceDcFileSync && $sourceDcFileSync->getOriginal())
		{
			continue;
		}

		$sourceDcFileSync = $fileSync;
	}

	if (!$sourceDcFileSync)
	{
		KalturaLog::info(">>> $assetId: NO_FILESYNC - No file sync to handle");
		return;
	}

	if ($sourceDcFileSync->getStatus() != flavorAsset::FLAVOR_ASSET_STATUS_READY)
	{
		KalturaLog::info(">>> $assetId: NOT_READY_" . $sourceDcFileSync->getStatus() . " - File sync " . $sourceDcFileSync->getId() . " is not ready skipping");
		return;
	}

	if ($targetDcFileSync)
	{
		KalturaLog::info(">>> $assetId: ALREADY_EXISTS_" . $targetDcFileSync->getStatus() . " - Found file sync " . $targetDcFileSync->getId() . " in target dc skipping");
		return;
	}

	try
	{
		KalturaLog::debug("Handling file sync " . $sourceDcFileSync->getId());
		$newfileSync = $sourceDcFileSync->cloneToAnotherStorage($targetDcId);
		$newfileSync->save();
		KalturaLog::info(">>> $assetId: CREATED - New file sync created " . $newfileSync->getId());
	}
	catch (Exception $e)
	{
		KalturaLog::info(">>> $assetId: FAILED - Could not create new file sync for [" . $fileSync->getId() . "] " . $e->getMessage());
	}
}

function handleAssets($assetIds, $sourceDcIds, $targetStorage, $partnerIds)
{
	$count = 0;

	foreach ($assetIds as $assetId)
	{
		$assetId = trim($assetId);
		if (!$assetId)
		{
			continue;
		}

		KalturaLog::debug('Retrieving asset ' . $assetId);
		$c = new Criteria();
		$c->add(assetPeer::ID, $assetId);
		$c->add(assetPeer::TYPE, assetPeer::retrieveAllFlavorsTypes(), Criteria::IN);
		$c->add(assetPeer::STATUS, flavorAsset::FLAVOR_ASSET_STATUS_READY, Criteria::IN);
		$asset = assetPeer::doSelectOne($c);

		if (!$asset)
		{
			KalturaLog::debug("Asset not found (or not READY) $assetId - skipping");
			continue;
		}

		if ($partnerIds && !in_array($asset->getPartnerId(), $partnerIds))
		{
			continue;
		}

		handleAsset($asset, $sourceDcIds, $targetStorage);

		kMemoryManager::clearMemory();
		$count++;
		if ($count % 1000 == 0 )
		{
			KalturaLog::debug("Sleeping 10 Seconds... count is $count");
			sleep(10);
		}
	}
}

function handleEntries($entryIds, $sourceDcIds, $targetStorage, $partnerIds)
{
	$count = 0;

	foreach ($entryIds as $entryId)
	{
		$entryId = trim($entryId);
		if (!$entryId)
		{
			continue;
		}

		$c = new Criteria();
		$c->add(entryPeer::ID, trim($entryId));
		$c->add(entryPeer::STATUS, entryStatus::READY);
		$entry = entryPeer::doSelectOne($c);
		if (!$entry)
		{
			KalturaLog::debug("Entry not found (or not READY) $entryId - skipping");
			continue;
		}

		if ($partnerIds && !in_array($entry->getPartnerId(), $partnerIds))
		{
			continue;
		}

		KalturaLog::debug('Retrieving non-source assets for entry ' . $entry->getId());
		$c = new Criteria();
		$c->add(assetPeer::ENTRY_ID, $entry->getId());
		$c->add(assetPeer::TYPE, assetPeer::retrieveAllFlavorsTypes(), Criteria::IN);
		$c->add(assetPeer::STATUS, flavorAsset::FLAVOR_ASSET_STATUS_READY, Criteria::IN);
		$c->addAnd(assetPeer::IS_ORIGINAL, false);
		$assets = assetPeer::doSelect($c);

		KalturaLog::debug('Found ' . count($assets) . ' non-source assets for entry ' . $entry->getId());

		foreach ($assets as /** @var flavorAsset $asset * */ $asset)
		{
			handleAsset($asset, $sourceDcIds, $targetStorage);
		}

		kMemoryManager::clearMemory();
		$count++;
		if ($count % 1000 == 0 )
		{
			KalturaLog::debug("Sleeping 20 Seconds... count is $count");
			sleep(20);
		}
	}
}

/**
 * @param $targetDcId
 * @param $filePath
 * @throws PropelException
 */
function main($sourceDcIds, $targetDcId, $filePath, $fileType, $partnerIds)
{
	KalturaLog::debug("Running for file [$filePath] and targetDcId [$targetDcId]");

	$ids = file($filePath);
	if (empty($ids))
	{
		KalturaLog::warning("File is empty - Exiting.");
		exit(1);
	}

	$targetStorage = StorageProfilePeer::retrieveByPK($targetDcId);
	if (!$targetStorage)
	{
		KalturaLog::warning("Storage [$targetDcId] does not exist");
		exit(1);
	}

	switch ($fileType)
	{
	case 'entry':
		handleEntries($ids, $sourceDcIds, $targetStorage, $partnerIds);
		break;

	case 'asset':
		handleAssets($ids, $sourceDcIds, $targetStorage, $partnerIds);
		break;

	default:
		echo "Invalid file type $fileType, must be entry/asset\n";
		exit(1);
	}

	KalturaLog::debug("DONE!");
}