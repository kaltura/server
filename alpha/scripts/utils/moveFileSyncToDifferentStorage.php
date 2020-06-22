<?php

if (count($argv) != 5)
{
	print("USAGE: <partnerId> <storageId> <lastUpdatedAt> <realrun-dryrun> ");
	exit(0);
}

define("BASE_DIR", dirname(__FILE__));
require_once(BASE_DIR.'/../../../alpha/scripts/bootstrap.php');

$partnerId = $argv[1];
$storageId = $argv[2];
$lastUpdatedAt = $argv[3];
$dryRun = $argv[4] != 'realrun';
if (!$storageId)
{
	KalturaLog::warning('No Storage Id');
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

main($partnerId, $storageId, $lastUpdatedAt);

/**
 * @param $partnerId
 * @param $storageId
 * @throws PropelException
 */
function main($partnerId, $storageId, $lastUpdatedAt)
{
	KalturaLog::debug("Running for PartnerId [$partnerId] and storageId [$storageId]");
	$externalStorage = StorageProfilePeer::retrieveByPK($storageId);
	if (!$externalStorage)
	{
		KalturaLog::warning("Storage [$storageId] does not exists");
		exit(0);
	}
	$partner = PartnerPeer::retrieveByPK($partnerId);
	if (!$partner)
	{
		KalturaLog::warning("Partner [$partnerId] does not exists");
		exit(0);
	}

	$lastHandledId = 0;
	//loop in 100 file_syncs cycles
	do
	{
		$criteria = new Criteria(FileSyncPeer::DATABASE_NAME);
		$criteria->add(FileSyncPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
		$criteria->add(FileSyncPeer::STATUS, FileSync::FILE_SYNC_STATUS_READY, Criteria::EQUAL);
		$criteria->add(FileSyncPeer::DC, kDataCenterMgr::getCurrentDcId(), Criteria::EQUAL);
		$criteria->add(FileSyncPeer::ID, $lastHandledId, Criteria::GREATER_THAN);
		$criteria->add(FileSyncPeer::UPDATED_AT, $lastUpdatedAt, Criteria::LESS_THAN);
		$criteria->add(FileSyncPeer::OBJECT_TYPE, FileSyncObjectType::ASSET);
		$criteria->add(FileSyncPeer::OBJECT_SUB_TYPE, flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$criteria->add(FileSyncPeer::FILE_PATH, 'NULL', Criteria::NOT_EQUAL);
		$criteria->addAscendingOrderByColumn(FileSyncPeer::ID);
		if ($lastHandledId == 0)
		{
			$criteria->setLimit(1);
		}
		else
		{
			$criteria->setLimit(100);
		}

		$fileSyncs = FileSyncPeer::doSelect($criteria);
		KalturaLog::debug("Found: " . count($fileSyncs) . " file syncs to copy");
		foreach ($fileSyncs as /** @var FileSync $fileSync * */ $fileSync)
		{
			try
			{
				KalturaLog::debug('Handling asset with id ' . $fileSync->getObjectId() . ' with fileSync id ' . $fileSync->getId());

				$asset = assetPeer::retrieveById($fileSync->getObjectId());
				if (!$asset || $asset->getIsOriginal())
				{
					KalturaLog::debug('Skipping file sync with id ' . $fileSync->getId() . ' and object id ' . $fileSync->getObjectId() . ' . Asset not found.');
				}
				else
				{
					if ($externalStorage->shouldExportFlavorAsset($asset))
					{
						$newfileSync = $fileSync->cloneToAnotherStorage($storageId);
						$newfileSync->save();
						KalturaLog::debug('New FileSync created ' . $newfileSync->getId());
					}
					else
					{
						KalturaLog::debug('Skipping exporting file sync with id ' . $fileSync->getId() . ' and object id ' . $fileSync->getObjectId());
					}
				}
			}
			catch (Exception $e)
			{
				KalturaLog::warning("Could not create newFileSync for fileSync [" . $fileSync->getId() . "]" . $e->getMessage());
			}
			$lastHandledId = $fileSync->getId();
		}
		kMemoryManager::clearMemory();

	} while (count($fileSyncs) > 0);
	KalturaLog::debug("DONE!");
}
