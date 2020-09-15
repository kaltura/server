<?php
if (count($argv) != 7) {
	echo "USAGE:  <last id file path> <ids amount> <source dcs> <dest dc> <results file path> <realrun-dryrun>\n";
	exit(0);
}

define("BASE_DIR", dirname(__FILE__));
define("CHUNK_SIZE", 10000);
require_once(BASE_DIR . '/../../../alpha/scripts/bootstrap.php');

$lastFileSyncFilePath = $argv[1];
$maxFileSyncId = getMaxFileSyncId($lastFileSyncFilePath);
$maxIdsAmount = $argv[2];
$sourceDcs = explode(',', $argv[3]);
$destDc = $argv[4];
$resultFilePath = $argv[5];
$dryRun = $argv[6] != 'realrun';

if ($dryRun)
{
	KalturaLog::debug('*************** In Dry run mode ***************');
}
else
{
	KalturaLog::debug('*************** In Real run mode ***************');
}
KalturaStatement::setDryRun($dryRun);

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
	$criteria->add(FileSyncPeer::STATUS, FileSync::FILE_SYNC_STATUS_READY);
	$criteria->addAnd(FileSyncPeer::DC, $sourceDcs, Criteria::IN);
	$criteria->addAnd(FileSyncPeer::ORIGINAL, 1);
	$criteria->addAnd(FileSyncPeer::ID, $maxFileSyncId, Criteria::LESS_THAN);
	$criteria->addAnd(FileSyncPeer::ID, max($maxFileSyncId - CHUNK_SIZE, 0), Criteria::GREATER_EQUAL);
	$criteria->addDescendingOrderByColumn(FileSyncPeer::ID);
	$fileSyncs = FileSyncPeer::doSelect($criteria);
	return $fileSyncs;
}

function getDestFileSync($fileSync, $destDc)
{
	$criteria = new Criteria(FileSyncPeer::DATABASE_NAME);
	$criteria->add(FileSyncPeer::OBJECT_TYPE, $fileSync->getObjectType());
	$criteria->add(FileSyncPeer::OBJECT_SUB_TYPE, $fileSync->getObjectSubType());
	$criteria->add(FileSyncPeer::PARTNER_ID, $fileSync->getPartnerId());
	$criteria->add(FileSyncPeer::OBJECT_ID, $fileSync->getObjectId());
	$criteria->add(FileSyncPeer::VERSION, $fileSync->getVersion());
	$criteria->add(FileSyncPeer::DC , $destDc);
	$destFileSync = FileSyncPeer::doSelectOne($criteria);
	return $destFileSync;
}

function createDestFileSync($fileSync, $destDc, $resultsFile)
{
	$objectId = $fileSync->getObjectId();
	try
	{
		KalturaLog::debug("Handling file sync " . $fileSync->getId() . " of object ID " . $objectId . " Type " . $fileSync->getObjectType());
		$newFileSync = $fileSync->cloneToAnotherStorage($destDc);
		$newFileSync->save();
		KalturaLog::info(">>> $objectId: CREATED - New file sync created " . $newFileSync->getId());
		$logLine = $fileSync->getId() . " " . $fileSync->getFilePath() . " " . $newFileSync->getId() . " " . $newFileSync->getFilePath() . PHP_EOL;
		fwrite($resultsFile, $logLine);
		return 1;
	}
	catch (Exception $e)
	{
		KalturaLog::info(">>> $objectId: FAILED - Could not create new file sync for [" . $fileSync->getId() . "] " . $e->getMessage());
		return 0;
	}
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

			$destFileSync = getDestFileSync($fileSync, $destDc);
			if (!$destFileSync)
			{
				$fileCreated = createDestFileSync($fileSync, $destDc, $resultsFile);
				if($fileCreated)
				{
					$idsNum++;
				}
			}
			else
			{
				KalturaLog::info(">>> " . $fileSync->getId() . ": ALREADY_EXISTS_" . $destFileSync->getStatus() . " - Found file sync " . $destFileSync->getId() . " in target dc skipping");
			}
		}
		$maxFileSyncId = max($maxFileSyncId - CHUNK_SIZE, 0);
	}

	fclose($resultsFile);
	file_put_contents($lastFileSyncFilePath, $lastFileSyncId);
	KalturaLog::debug("Exported [$idsNum] ids. Last file sync id [$lastFileSyncId]");
	KalturaLog::debug("DONE!");
}