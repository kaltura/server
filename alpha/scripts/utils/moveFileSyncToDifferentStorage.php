<?php

define("BASE_DIR", dirname(__FILE__));
require_once(BASE_DIR.'/../../../alpha/scripts/bootstrap.php');

if (count($argv) != 4)
{
	KalturaLog::debug("USAGE: <partnerId> <storageId> <realrun-dryrun> ");
	exit(0);
}

$partnerId = $argv[1];
$storageId = $argv[2];
if (!$storageId)
{
	KalturaLog::debug(" No Stroge Id");
	exit(0);
}

$realRun = isset($argv[3]) && $argv[3] == 'realrun';
if ($realRun)
{
	KalturaLog::debug("*************** In Realrun mode ***************");
}
else
{
	KalturaLog::debug("*************** In Dry Run mode ***************");
}

main($partnerId, $storageId, $realRun);

/**
 * @param $partnerId
 * @param $storageId
 * @param $realRun
 * @throws PropelException
 */
function main($partnerId, $storageId,$realRun)
{
	KalturaLog::debug("Running for PartnerId [$partnerId] and storageId [$storageId]");
	$partner = PartnerPeer::retrieveByPK($partnerId);
	if (!$partner)
	{
		KalturaLog::debug("Partner [$partnerId] does not exists ");
		exit(0);
	}

	$criteria = new Criteria(FileSyncPeer::DATABASE_NAME);
	$criteria->add(FileSyncPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
	$criteria->add(FileSyncPeer::STATUS, FileSync::FILE_SYNC_STATUS_READY, Criteria::EQUAL);
	$criteria->add(FileSyncPeer::OBJECT_TYPE, FileSyncObjectType::ASSET);
	$criteria->add(FileSyncPeer::OBJECT_SUB_TYPE, flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
	$criteria->add(FileSyncPeer::FILE_PATH, 'NULL', Criteria::NOT_EQUAL);
	$fileSyncs = FileSyncPeer::doSelect($criteria);
	KalturaLog::debug("Founc: " . count($fileSyncs) . " file syncs to copy");
	foreach ($fileSyncs as $fileSync)
	{
		/** @var FileSync $fileSync */
		KalturaLog::debug('Handling file sync with id ' . $fileSync->getId());
		//create new fileSync With status pending and new storageId
		$newfileSync = $fileSync->copy(true);
		$newfileSync->setStatus(FileSync::FILE_SYNC_STATUS_PENDING);
		$newfileSync->setDc($storageId);
		$newfileSync->setSrcPath($fileSync->getFullPath());
		$newfileSync->setSrcEncKey($fileSync->getSrcEncKey());
		$newfileSync->setFileType(FileSync::FILE_SYNC_FILE_TYPE_URL);
		if ($realRun)
		{
			$newfileSync->save();
		}
		else
		{
			KalturaLog::debug("Would update new file sync to be: " . print_r($newfileSync, true));
		}
	}
}
