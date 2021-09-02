<?php

function debug($level, $str)
{
	print("SCRIPT-DEBUG: $level: $str\n");
}

function getEntryIds($partnerId)
{
	$c = new Criteria();
	$c->addSelectColumn(entryPeer::ID);
	$c->add ( entryPeer::PARTNER_ID , $partnerId , Criteria::EQUAL );
	$c->add ( entryPeer::STATUS , entryStatus::READY , Criteria::EQUAL );
	$res = entryPeer::doSelectStmt( $c );
	return $res->fetchAll(PDO::FETCH_COLUMN);
}

function getFileSync($flavorAsset)
{
	$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
	$c = FileSyncPeer::getCriteriaForFileSyncKey($syncKey);
	$c->addAnd ( FileSyncPeer::FILE_SIZE , 0, Criteria::GREATER_THAN );
	return FileSyncPeer::doSelectOne($c);
}

//*******************************************************************************************************************

if ($argc < 4)
{
	die ($argv[0]. " <PartnerID> <NumUpdatesBeforeSleep> <dry-run/real-run> [MaxUpdates]\n");
}

$startTime = microtime(true);

require_once(__DIR__ . '/../bootstrap.php');

$partnerId = $argv[1];
$numUpdatesBeforeSleep = $argv[2];
$realRun = ($argv[3] === 'real-run');

KalturaStatement::setDryRun(!$realRun);

$maxUpdates = 0;
if($argc > 4)
{
	$maxUpdates = $argv[4];
}

if (!PartnerPeer::retrieveByPK($partnerId))
{
	die ("Partner ID not found\n");
}

$entryIds = getEntryIds($partnerId);

$entryCount = count($entryIds);
debug("INFO", "EntryCount ($entryCount)");

$assetsUpdated = 0;
$entryIndex = 0;
foreach ($entryIds as $entryId)
{
	$flavorAssets = assetPeer::retrieveReadyFlavorsByEntryIdAndType($entryId, array(assetType::FLAVOR));

	/* @var $flavorAsset asset */
	foreach ($flavorAssets as $flavorAsset)
	{
		$sizeInBytes = $flavorAsset->getSizeInBytes();
		if($sizeInBytes)
		{
			debug("INFO", "skip ({$flavorAsset->getId()}) (entryId $entryId), already has SizeInBytes ($sizeInBytes)");
			continue;
		}

		$fileSync = getFileSync($flavorAsset);
		if(!$fileSync)
		{
			debug("ERROR", "flavor asset ({$flavorAsset->getId()}) (entryId $entryId) has no ready file sync with a valid size");
			continue;
		}

		$sizeInBytes = $fileSync->getFileSize();

		$flavorAsset->setSizeInBytes($sizeInBytes);
		$flavorAsset->save();

		debug("INFO", "Updated FlavorAssetId ({$flavorAsset->getId()}) (entryId $entryId) with SizeInBytes ($sizeInBytes)");

		$assetsUpdated++;
		if($assetsUpdated % $numUpdatesBeforeSleep == 0)
		{
			debug("INFO", "Saved so far ($assetsUpdated), Scanned ($entryIndex) entries");
			kMemoryManager::clearMemory();
			if($realRun)
			{
				sleep(10);
			}
		}
	}

	$entryIndex++;

	if( ($maxUpdates) && ($assetsUpdated >= $maxUpdates) )
	{
		debug("INFO", "Reached max updates ($maxUpdates)");
		break;
	}
}

debug("INFO", "Updated ($assetsUpdated) flavor assets");
$secondsElapsed = microtime(true) - $startTime;
debug("INFO", "Took ($secondsElapsed) seconds");
die("DONE\n");
