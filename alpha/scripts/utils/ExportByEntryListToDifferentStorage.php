<?php

if (count($argv) != 5)
{
	print("USAGE: <sourceDc> <storageId> <fileName> <realrun-dryrun> ");
	exit(0);
}

define("BASE_DIR", dirname(__FILE__));
require_once(BASE_DIR.'/../../../alpha/scripts/bootstrap.php');

$sourceDc = $argv[1];
$storageId = $argv[2];
$filePath = $argv[3];
$dryRun = $argv[4] != 'realrun';

if (!$storageId)
{
	KalturaLog::warning('No Storage Id');
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

main($sourceDc, $storageId, $filePath);

/**
 * @param $sourceDc
 * @param $storageId
 * @param $filePath
 * @throws PropelException
 */
function main($sourceDc, $storageId, $filePath)
{
	KalturaLog::debug("Running for file [$filePath] and storageId [$storageId] and source dc $sourceDc");
	$content = file_get_contents($filePath);
	if (!trim($content))
	{
		KalturaLog::warning("File is empty - Exiting.");
		exit(0);
	}

	$externalStorage = StorageProfilePeer::retrieveByPK($storageId);
	if (!$externalStorage)
	{
		KalturaLog::warning("Storage [$storageId] does not exists");
		exit(0);
	}

	$entryIds = explode("\n", $content);

	foreach ($entryIds as $entryId)
	{
		$entry = entryPeer::retrieveByPK(trim($entryId));
		if (!$entry)
		{
			KalturaLog::debug("Entry not found $entryId - skipping");
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
				$criteria->add(FileSyncPeer::PARTNER_ID, $asset->getPartnerId(), Criteria::EQUAL);
				$criteria->add(FileSyncPeer::OBJECT_ID, $asset->getId(), Criteria::EQUAL);
//				$criteria->add(FileSyncPeer::STATUS, FileSync::FILE_SYNC_STATUS_READY, Criteria::EQUAL);
				$criteria->add(FileSyncPeer::DC, array($sourceDc, $storageId) , Criteria::IN);
				$criteria->add(FileSyncPeer::OBJECT_TYPE, FileSyncObjectType::ASSET);
				$criteria->add(FileSyncPeer::OBJECT_SUB_TYPE, flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
//				$criteria->add(FileSyncPeer::FILE_PATH, 'NULL', Criteria::NOT_EQUAL);
				$criteria->addAscendingOrderByColumn(FileSyncPeer::ID);
				$fileSyncs = FileSyncPeer::doSelect($criteria);

				$remoteDcFileSyncFound = false;
				foreach ($fileSyncs as /** @var FileSync $fileSync * */ $fileSync)
				{
					if ($fileSync->getDc() === $storageId)
					{
						$remoteDcFileSyncFound = true;
					}
				}
				if ($remoteDcFileSyncFound )
				{
					KalturaLog::debug("Found file sync in remote dc [$storageId] for assetId " . $asset->getId() . " . skipping exporting asset");
					continue;
				}
				KalturaLog::debug("Found: " . count($fileSyncs) . " file syncs to copy");
				foreach ($fileSyncs as /** @var FileSync $fileSync * */ $fileSync)
				{
					if ($fileSync->getStatus() != flavorAsset::FLAVOR_ASSET_STATUS_READY)
					{
						KalturaLog::debug("Found filesync " . $fileSync->getId() . " with status  " . $fileSync->getStatus() . " - skipping filesync");
						continue;
					}
					try
					{
						KalturaLog::debug("Handling filesync " . $fileSync->getId());
						$newfileSync = $fileSync->cloneToAnotherStorage($storageId);
						$newfileSync->save();
						KalturaLog::debug('New FileSync created ' . $newfileSync->getId());
					}
					catch (Exception $e)
					{
						KalturaLog::warning("Could not create newFileSync for fileSync [" . $fileSync->getId() . "]" . $e->getMessage());
					}
				}
			}
			else
			{
				KalturaLog::debug('Asset ' . $asset->getId() . ' should not be Exported to remote storge ' . $storageId);
			}
		}
	}
	KalturaLog::debug("DONE!");
}
