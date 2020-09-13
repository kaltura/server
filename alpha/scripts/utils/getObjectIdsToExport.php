<?php
if (count($argv) != 6) {
	echo "USAGE:  <last id file path> <ids amount> <source dcs> <dest dc> <results file path>\n";
	exit(0);
}

require_once(BASE_DIR . '/../../../alpha/scripts/bootstrap.php');

define("BASE_DIR", dirname(__FILE__));
define("CHUNK_SIZE", 10000);

$lastFileSyncFilePath = $argv[1];
$maxFileSyncId = getMaxFileSyncId($lastFileSyncFilePath);
$maxIdsAmount = $argv[2];
$sourceDcs = explode(',', $argv[3]);
$destDc = $argv[4];
$resultFilePath = $argv[5];

main($maxFileSyncId, $maxIdsAmount, $sourceDcs, $destDc, $resultFilePath, $lastFileSyncFilePath);

function getMaxFileSyncId($lastRunFilePath)
{
	$startFromId = trim(file_get_contents($lastRunFilePath));
	if(!$startFromId)
	{
		throw new Exception ("Missing file with file sync id for last run");
	}
	return $startFromId;
}

function getRangedFileSyncs($maxFileSyncId, $sourceDcs)
{
	$criteria = new Criteria(FileSyncPeer::DATABASE_NAME);
	$criteria->add(FileSyncPeer::OBJECT_TYPE, FileSyncObjectType::ASSET);
	$criteria->add(FileSyncPeer::STATUS, FileSync::FILE_SYNC_STATUS_READY);
	$criteria->add(FileSyncPeer::OBJECT_SUB_TYPE, flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
	$criteria->addAnd(FileSyncPeer::DC, $sourceDcs, Criteria::IN);
	$criteria->addAnd(FileSyncPeer::ID, $maxFileSyncId, Criteria::LESS_EQUAL);
	$criteria->addAnd(FileSyncPeer::ID, $maxFileSyncId - CHUNK_SIZE, Criteria::GREATER_EQUAL);
	$fileSyncs = FileSyncPeer::doSelect($criteria);
	return $fileSyncs;
}

function getDestFileSync($asset, $destDc)
{
	$syncKey = $asset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
	$c = FileSyncPeer::getCriteriaForFileSyncKey($syncKey);
	$c->addAnd (FileSyncPeer::DC , $destDc);
	$destFileSync = FileSyncPeer::doSelectOne($c);
	return $destFileSync;
}

function main($maxFileSyncId, $maxIdsAmount, $sourceDcs, $destDc, $resultFilePath, $lastFileSyncFilePath)
{
	KalturaLog::debug("Writing results to file [$resultFilePath] max file sync id [$maxFileSyncId] max ids to return [$maxIdsAmount]");

	$idsNum = 0;
	$lastFileSyncId = $maxFileSyncId;
	$resultsFile = fopen($resultFilePath,'w');
	while ($idsNum < $maxIdsAmount && $maxFileSyncId > 0)
	{
		$fileSyncs = getRangedFileSyncs($maxFileSyncId, $sourceDcs);
		foreach ($fileSyncs as $fileSync)
		{
			$lastFileSyncId = $fileSync->getId();
			if($idsNum >= $maxIdsAmount)
			{
				break;
			}

			$asset = assetPeer::retrieveById($fileSync->getObjectId());
			if(!$asset || in_array($asset->getStatus(), array(asset::ASSET_STATUS_DELETED, asset::ASSET_STATUS_ERROR, asset::ASSET_STATUS_NOT_APPLICABLE)))
			{
				continue;
			}

			$destFileSync = getDestFileSync($asset, $destDc);
			if (!$destFileSync)
			{
				fwrite($resultsFile,$asset->getId() . PHP_EOL);
				$idsNum++;
			}
		}
		$maxFileSyncId = $maxFileSyncId - CHUNK_SIZE;
	}

	if($maxFileSyncId <= 0)
	{
		$lastFileSyncId = 0;
	}

	fclose($resultsFile);
	file_put_contents($lastFileSyncFilePath, $lastFileSyncId);
	KalturaLog::debug("Found [$idsNum] ids. last file sync id [$lastFileSyncId]");
	KalturaLog::debug("DONE!");
}