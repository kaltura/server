<?php

if (count($argv) != 5)
{
	print("USAGE: <sourceDc> <storageId> <fileName> <realrun-dryrun> ");
	exit(0);
}

define("BASE_DIR", dirname(__FILE__));
require_once(BASE_DIR.'/../../../alpha/scripts/bootstrap.php');

$sourceDc = $argv[1];
$storageIdDest = $argv[2];
$filePath = $argv[3];
$dryRun = $argv[4] != 'realrun';

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

main($sourceDc, $storageIdDest, $filePath);

/**
 * @param $sourceDc
 * @param $storageIdDest
 * @param $filePath
 * @throws PropelException
 */
function main($sourceDc, $storageIdDest, $filePath)
{
	KalturaLog::debug("Running for file [$filePath] and storageIdDest [$storageIdDest] and source dc $sourceDc");
	$count = 0;
	$entryIds = file($filePath);
	if (empty($entryIds))
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

	foreach ($entryIds as $entryId)
	{
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
		$c->add(assetPeer::TYPE, array(assetType::FLAVOR), Criteria::IN);
		$c->add(assetPeer::STATUS, flavorAsset::FLAVOR_ASSET_STATUS_READY, Criteria::IN);
		$c->addAnd(assetPeer::IS_ORIGINAL, false);
		$assets = assetPeer::doSelect($c);

		KalturaLog::debug('Found ' . count($assets) . ' non-source assets for entry ' . $entry->getId());

		foreach ($assets as /** @var flavorAsset $asset * */ $asset)
		{
			KalturaLog::debug('Handling asset with id ' . $asset->getId());
			if ($externalStorage->shouldExportFlavorAsset($asset))
			{
				$criteria = new Criteria(FileSyncPeer::DATABASE_NAME);
				$criteria->add(FileSyncPeer::OBJECT_ID, $asset->getId(), Criteria::EQUAL);
				$criteria->add(FileSyncPeer::OBJECT_TYPE, FileSyncObjectType::ASSET);
				$criteria->add(FileSyncPeer::OBJECT_SUB_TYPE, flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
				$criteria->add(FileSyncPeer::DELETED_ID, null, Criteria::ISNULL);
				$criteria->add(FileSyncPeer::DC, array($sourceDc, $storageIdDest), Criteria::IN);
				$criteria->add(FileSyncPeer::PARTNER_ID, $asset->getPartnerId(), Criteria::EQUAL);
				$criteria->addDescendingOrderByColumn(FileSyncPeer::VERSION);
				$criteria->addAscendingOrderByColumn(FileSyncPeer::DC);
				$fileSyncs = FileSyncPeer::doSelect($criteria);

				$remoteDcFileSyncFound = false;
				$maxVersion = -1;
				$fileSyncToHandle = null;
				//Get the Max Version and Local fileSync to handle
				foreach ($fileSyncs as /** @var FileSync $fileSync * */ $fileSync)
				{
					if ($fileSync->getDc() == $sourceDc && $fileSync->getFileSize() > 0 && $fileSync->getVersion() > $maxVersion)
					{
						$fileSyncToHandle = $fileSync;
					}
				}
				if (!$fileSyncToHandle)
				{
					KalturaLog::debug('No filesync to handle for flavor asset ' . $asset->getId());
					continue;
				}

				// look for a sibling with same version on the remote storage
				foreach ($fileSyncs as /** @var FileSync $fileSync * */ $fileSync)
				{
					if ($fileSync->getDc() === $storageIdDest && $fileSync->getVersion() == $fileSyncToHandle->getVersion())
					{
						$remoteDcFileSyncFound = true;
					}
				}

				if ($remoteDcFileSyncFound)
				{
					KalturaLog::debug("Found file sync in remote dc [$storageIdDest] for assetId " . $asset->getId() . " . skipping exporting asset");
					continue;
				}

				if ($fileSyncToHandle->getStatus() != flavorAsset::FLAVOR_ASSET_STATUS_READY)
				{
					KalturaLog::debug("Filesync " . $fileSyncToHandle->getId() . " in not ready " . $fileSyncToHandle->getStatus() . " - skipping asset");
					continue;
				}
				try
				{
					KalturaLog::debug("Handling filesync " . $fileSyncToHandle->getId());
					$newfileSync = $fileSyncToHandle->cloneToAnotherStorage($storageIdDest);
					$newfileSync->save();
					KalturaLog::debug('New FileSync created ' . $newfileSync->getId());
				}
				catch (Exception $e)
				{
					KalturaLog::warning("Could not create newFileSync for fileSync [" . $fileSync->getId() . "]" . $e->getMessage());
				}
			}
			else
			{
				KalturaLog::debug('Asset ' . $asset->getId() . ' should not be Exported to remote storge ' . $storageIdDest);
			}
		}

		kMemoryManager::clearMemory();
		$count++;
		if ($count % 1000 == 0 )
		{
			KalturaLog::debug("Sleeping 60 Seconds... count is $count");
			sleep(60);
		}
	}
	KalturaLog::debug("DONE!");
}
