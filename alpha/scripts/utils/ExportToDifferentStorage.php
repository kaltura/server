<?php

if (count($argv) != 5)
{
	echo "USAGE: <storageId> <fileName> <type - entry/asset> <realrun-dryrun>\n";
	exit(0);
}

define("BASE_DIR", dirname(__FILE__));
require_once(BASE_DIR.'/../../../alpha/scripts/bootstrap.php');

$storageIdDest = $argv[1];
$filePath = $argv[2];
$fileType = $argv[3];
$dryRun = $argv[4] != 'realrun';

if (!is_numeric($storageIdDest))
{
	KalturaLog::warning("Destination Storage id should be numeric");
	exit(0);
}

if (!file_exists($filePath))
{
	KalturaLog::warning("File $filePath Does not exists. Exiting...");
	exit(0);
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

main($storageIdDest, $filePath, $fileType);


function handleAsset($asset, $externalStorage)
{
	$assetId = $asset->getId();

	$storageIdDest = $externalStorage->getId();
	KalturaLog::debug('Handling asset ' . $assetId);

	if (!$externalStorage->shouldExportFlavorAsset($asset))
	{
		KalturaLog::info(">>> $assetId: NO_EXPORT - Asset should not be exported to remote storage " . $storageIdDest);
		return;
	}

	$criteria = new Criteria(FileSyncPeer::DATABASE_NAME);
	$criteria->add(FileSyncPeer::OBJECT_ID, $asset->getId(), Criteria::EQUAL);
	$criteria->add(FileSyncPeer::OBJECT_TYPE, FileSyncObjectType::ASSET);
	$criteria->add(FileSyncPeer::VERSION, $asset->getVersion());
	$criteria->add(FileSyncPeer::OBJECT_SUB_TYPE, flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
	$criteria->add(FileSyncPeer::DELETED_ID, 0, Criteria::EQUAL);
	$fileSyncs = FileSyncPeer::doSelect($criteria);

	$remoteDcFileSync = null;
	$fileSyncToHandle = null;

	//Get the Local fileSync to handle
	foreach ($fileSyncs as /** @var FileSync $fileSync * */ $fileSync)
	{
		if ($fileSync->getOriginal() && $fileSync->getFileSize() > 0)
		{
			$fileSyncToHandle = $fileSync;
		}

		if ($fileSync->getDc() == $storageIdDest)
		{
			$remoteDcFileSync = $fileSync;
		}
	}

	if (!$fileSyncToHandle)
	{
		KalturaLog::info(">>> $assetId: NO_FILESYNC - No file sync to handle");
		return;
	}

	if ($fileSyncToHandle->getStatus() != flavorAsset::FLAVOR_ASSET_STATUS_READY)
	{
		KalturaLog::info(">>> $assetId: NOT_READY - File sync " . $fileSyncToHandle->getId() . " is not ready " . $fileSyncToHandle->getStatus() . " skipping");
		return;
	}

	if ($remoteDcFileSync)
	{
		KalturaLog::info(">>> $assetId: ALREADY_EXISTS - Found file sync " . $remoteDcFileSync->getId() . " status " . $remoteDcFileSync->getStatus() . " in target dc [$storageIdDest] skipping");
		return;
	}

	try
	{
		KalturaLog::debug("Handling file sync " . $fileSyncToHandle->getId());
		$newfileSync = $fileSyncToHandle->cloneToAnotherStorage($storageIdDest);
		$newfileSync->save();
		KalturaLog::info(">>> $assetId: CREATED - New file sync created " . $newfileSync->getId());
	}
	catch (Exception $e)
	{
		KalturaLog::info(">>> $assetId: FAILED - Could not create new file sync for [" . $fileSync->getId() . "] " . $e->getMessage());
	}
}

function handleAssets($assetIds, $externalStorage)
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
			continue;
		}

		handleAsset($asset, $externalStorage);

		kMemoryManager::clearMemory();
		$count++;
		if ($count % 1000 == 0 )
		{
			KalturaLog::debug("Sleeping 10 Seconds... count is $count");
			sleep(10);
		}
	}
}

function handleEntries($entryIds, $externalStorage)
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
			KalturaLog::debug("Entry not found (Or not in status READY) $entryId - skipping");
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
			handleAsset($asset, $externalStorage);
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
 * @param $storageIdDest
 * @param $filePath
 * @throws PropelException
 */
function main($storageIdDest, $filePath, $fileType)
{
	KalturaLog::debug("Running for file [$filePath] and storageIdDest [$storageIdDest]");

	$ids = file($filePath);
	if (empty($ids))
	{
		KalturaLog::warning("File is empty - Exiting.");
		exit(0);
	}

	$externalStorage = StorageProfilePeer::retrieveByPK($storageIdDest);
	if (!$externalStorage)
	{
		KalturaLog::warning("Storage [$storageIdDest] does not exists");
		exit(0);
	}

	switch ($fileType)
	{
	case 'entry':
		handleEntries($ids, $externalStorage);
		break;

	case 'asset':
		handleAssets($ids, $externalStorage);
		break;

	default:
		echo "Invalid file type $fileType, must be entry/asset\n";
		exit(0);
	}

	KalturaLog::debug("DONE!");
}