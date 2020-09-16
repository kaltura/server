<?php
require_once('/opt/kaltura/app/alpha/scripts/bootstrap.php');
require_once('/opt/kaltura/app/alpha/config/kConf.php');
require_once('/opt/kaltura/app/infra/storage/kFileBase.php');
require_once('/opt/kaltura/app/infra/storage/kFile.class.php');

class deleteFilesWorker
{
    protected $con;
	protected $dryRun;
	protected $minFileSize;
	protected $efsFilePath;
	protected $minUpdatedAtTime;
	const STOP_FILE_PATH = '/tmp/deleteScript.stop';
	const FILE_SYNC_READY_STATUS = 2;
	const FILE_SYNC_PURGE_STATUS = 4;
	const MAP_NAME = 'cloud_storage';
	const EFS_DC = 1;
	const AWS_DC = 2;
	const MAX_UPDATED_AT_PARAM = 'min_updated_at';
	const ITERATION_FILESYNC_LIMIT = 'iteration_filesync_limit';
	const SCRIPT_MS_RUNNING_TIME_THRESHOLD = 30000;
	const SLEEP_BETWEEN_FAST_ITERATIONS = 120;
	const MIN_FILE_SYNC_ID_TIME_WINDOW = 600;

    public function __construct($minFileSize, $efsFilePath, $minUpdatedAtTime, $dryRun)
	{
		$this->con =myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
		$this->minFileSize = $minFileSize;
		$this->efsFilePath = $efsFilePath;
		$this->minUpdatedAtTime = $minUpdatedAtTime;
		$this->dryRun = $dryRun;
	}

	public function doWork()
	{
		while(!kFile::checkFileExists(self::STOP_FILE_PATH))
		{
			KalturaLog::info("Starting new deletion iteration");
			$iterationStartTime = microtime(true);
			$minFileSyncId = $this->getMinFileSyncId();
			$bytesDeleted = 0;
			if($minFileSyncId)
			{
				$candidatesFilesSync = $this->getCandidatesForDelete($minFileSyncId);
				$efsFilesSyncObjects = $this->transformFileSyncToObjectsArray($candidatesFilesSync);
				$efsFilesSyncObjects = $this->filterEfsFileObjects($efsFilesSyncObjects);
				foreach ($efsFilesSyncObjects as $objectId => $fileSync)
				{
					$bytesDeleted += $this->deleteFile($fileSync);
				}
			}

			kMemoryManager::clearMemory();
			$execution_time = (microtime(true) - $iterationStartTime);
			KalturaLog::info("Finished a deletion iteration in {$execution_time} ms, {$bytesDeleted} bytes were deleted");
			if($execution_time < self::SCRIPT_MS_RUNNING_TIME_THRESHOLD)
			{
				sleep(self::SLEEP_BETWEEN_FAST_ITERATIONS);
			}
		}

		KalturaLog::warning('Found stop file, finishing delete script');
	}

	protected function transformFileSyncToObjectsArray($fileSyncs)
	{
		$object_ids = array();
		foreach ($fileSyncs as $fileSync)
		{
			/* @var $fileSync fileSync */
			$object_ids[$fileSync->getObjectId()] = $fileSync;
		}

		return $object_ids;
	}

	protected function getCandidatesForDelete($minFileSyncId)
	{
		KalturaLog::info("Getting efs file syncs");
		$maxUpdatedAt = kConf::get(self::MAX_UPDATED_AT_PARAM,self::MAP_NAME, 12*3600);
		$iterationFileSyncLimit = kConf::get(self::ITERATION_FILESYNC_LIMIT,self::MAP_NAME, 1000);
		$criteria = new Criteria();
		$criteria->add(FileSyncPeer::ID, $minFileSyncId, Criteria::GREATER_EQUAL);
		$criteria->add(FileSyncPeer::OBJECT_TYPE, FileSyncObjectType::ASSET);
		$criteria->add(FileSyncPeer::OBJECT_SUB_TYPE, flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		$criteria->add(FileSyncPeer::STATUS, self::FILE_SYNC_READY_STATUS, Criteria::EQUAL);
		$criteria->add(FileSyncPeer::FILE_PATH, $this->efsFilePath, Criteria::LIKE);
		$criteria->add(FileSyncPeer::DC, self::EFS_DC, Criteria::EQUAL);
		$criteria->add(FileSyncPeer::FILE_SIZE, $this->minFileSize, Criteria::GREATER_THAN);
		$criteria->add(FileSyncPeer::UPDATED_AT,time() - $maxUpdatedAt, Criteria::LESS_THAN);
		$criteria->addAscendingOrderByColumn(FileSyncPeer::ID);
		$criteria->setLimit($iterationFileSyncLimit);
		$result = null;
		try
		{
			$result = FileSyncPeer::doSelect($criteria, $this->con);
		}
		catch(PropelException $ex)
		{
			KalturaLog::err('Failed to get efs file syncs');
			KalturaLog::err($ex);
		}

		if($result)
		{
			$numberOfCandidates = count($result);
			KalturaLog::info("Found {$numberOfCandidates} efs candidates for deletion");
		}
		else
		{
			KalturaLog::info("Have not found efs candidates for deletion");
		}

		return $result;
	}

	protected function getMinFileSyncIdQuery($minUpdatedAt)
	{
		$criteria = new Criteria();
		$criterion = $criteria->getNewCriterion(FileSyncPeer::UPDATED_AT, $minUpdatedAt,Criteria::GREATER_EQUAL);
		$criterion->addAnd($criteria->getNewCriterion(FileSyncPeer::UPDATED_AT,$minUpdatedAt + self::MIN_FILE_SYNC_ID_TIME_WINDOW,Criteria::LESS_THAN));
		$criteria->addAnd($criterion);
		$criteria->add(FileSyncPeer::OBJECT_TYPE, FileSyncObjectType::ASSET);
		$criteria->add(FileSyncPeer::OBJECT_SUB_TYPE, flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		$criteria->add(FileSyncPeer::STATUS, self::FILE_SYNC_READY_STATUS, Criteria::EQUAL);
		$criteria->add(FileSyncPeer::FILE_PATH, $this->efsFilePath, Criteria::LIKE);
		$criteria->add(FileSyncPeer::DC, self::EFS_DC, Criteria::EQUAL);
		$criteria->add(FileSyncPeer::FILE_SIZE, $this->minFileSize, Criteria::GREATER_THAN);
		$criteria->addAscendingOrderByColumn(FileSyncPeer::UPDATED_AT);
		return $criteria;
	}

	protected function getMinFileSyncId()
	{
		KalturaLog::info('Looking for minimum file id');
		$minTime = time() - $this->minUpdatedAtTime;
		$fileSync = null;
		$maxUpdatedAt = kConf::get(self::MAX_UPDATED_AT_PARAM,self::MAP_NAME, 12*3600);
		while($minTime < (time() - $maxUpdatedAt))
		{
			try
			{
				$criteria = $this->getMinFileSyncIdQuery($minTime);
				$fileSync = FileSyncPeer::doSelectOne($criteria, $this->con);
				if($fileSync)
				{
					$fileSyncId = $fileSync->getId();
					KalturaLog::info("found file sync with id {$fileSyncId}");
					return $fileSyncId;
				}

				$minTime = $minTime + self::MIN_FILE_SYNC_ID_TIME_WINDOW;
			}
			catch (PropelException $ex)
			{
				KalturaLog::err('Failed to get efs file syncs');
				KalturaLog::err($ex);
			}
		}

		KalturaLog::info('Have not found a minimum file sync id going to sleep');
		sleep(self::SLEEP_BETWEEN_FAST_ITERATIONS);
		return null;
	}

	/**
	 * @param string $efsFilePath
	 * @param string $s3Location
	 * @return bool
	 */
	protected function verifyFileSize($efsFilePath, $s3Location)
	{
		try
		{
			exec("/opt/kaltura/scripts/deleteEfsFiles/getSizeS3.sh " . $s3Location, $sizeS3);
			$S3Size =  end($sizeS3);
			$efsSize = kFile::fileSize($efsFilePath);
			if ($efsSize == $S3Size)
			{
				if($this->dryRun)
				{
					KalturaLog::info("{$efsFilePath} size {$efsSize} and {$s3Location} size {$S3Size} match");
				}

				return true;
			}
		}
		catch(Exception $ex)
		{
			KalturaLog::err("Error while trying to find size for {$efsFilePath} and {$s3Location}");
			KalturaLog::err($ex);
			return false;
		}

		KalturaLog::info("{$efsFilePath} size {$efsSize} and {$s3Location} size {$S3Size} doesnt match, skipping");
		return false;
	}

	/**
	 * @param fileSync $efsFileSync
	 * @return int
	 */
	protected function deleteFile($efsFileSync)
	{
		$efsFilePath = $efsFileSync->getFullPath();
		$efsFileSize = $efsFileSync->getFileSize();
		if($this->dryRun)
		{
			KalturaLog::info("DRY RUN - Deleting file {$efsFilePath}, fileSync ID:{$efsFileSync->getId()}, size {$efsFileSize}");
		}
		else
		{
			KalturaLog::info("Deleting file {$efsFilePath}, fileSync ID:{$efsFileSync->getId()}, size {$efsFileSize}");
			$last_line = system("rm -rf {$efsFilePath}", $retval);
			if($last_line)
			{
				$efsFileSync->setStatus(self::FILE_SYNC_PURGE_STATUS);
				try
				{
					$efsFileSync->save();
				}
				catch(PropelException $ex)
				{
					KalturaLog::err("Error updating fileSync {$efsFileSync->getId()} to status purged");
					KalturaLog::err($ex);
					return 0;
				}

				KalturaLog::info("Delete command output: {$last_line} ");
				return $efsFileSize;
			}
			else
			{
				KalturaLog::err("Error deleting {$efsFilePath}");
			}
		}

		return 0;
	}

	protected function verifyFlavorAsset($objectId)
	{
		$flavorAsset = assetPeer::retrieveById($objectId, $this->con);
		if(!$flavorAsset)
		{
			KalturaLog::info("No flavor asset for $objectId skipping");
			return false;
		}

		if($flavorAsset->getIsOriginal() == 1)
		{
			KalturaLog::info("Original flavor $objectId skipping");
			return false;
		}

		return true;
	}

	protected function getAwsFileSync($efsFileSyncObjectIds)
	{
		KalturaLog::info("Getting AWS file syncs");
		$result = null;
		$criteria = new Criteria();
		$criteria->add(FileSyncPeer::DC, self::AWS_DC, Criteria::EQUAL);
		$criteria->add(FileSyncPeer::STATUS, self::FILE_SYNC_READY_STATUS, Criteria::EQUAL);
		$criteria->add(FileSyncPeer::OBJECT_ID, $efsFileSyncObjectIds, Criteria::IN);
		try
		{
			$result = FileSyncPeer::doSelect($criteria, $this->con);
		}
		catch(PropelException $ex)
		{
			KalturaLog::err('Failed to get aws file syncs');
			KalturaLog::err($ex);
		}

		return $result;
	}

	protected function filterEfsFileObjects($efsFileSyncObjects)
	{
		$result = array();
		$s3FileSyncs = $this->getAwsFileSync(array_keys($efsFileSyncObjects));
		//Now that we found siblings in DC 2
		foreach ($s3FileSyncs as $s3FileSync)
		{
			/* @var $s3FileSync fileSync */
			$objectId = $s3FileSync->getObjectId();
			if($this->verifyFlavorAsset($objectId) && isset($efsFileSyncObjects[$objectId]))
			{
				if($this->verifyFileSize($efsFileSyncObjects[$objectId]->getFullPath(), $s3FileSync->getFullPath()))
				{
					$result[$objectId] = $efsFileSyncObjects[$objectId];
				}
			}
		}

		if($result)
		{
			$numberOfObjects = count($result);
			KalturaLog::info("There are {$numberOfObjects} objects for deletion after filtering");
		}
		else
		{
			KalturaLog::info("Not file sync to delete after filtering");
		}

			return $result;
	}
}

if($argc < 4)
{
	echo "Usage: php $argv[0] [efsFilePath] [minimum file size] [minimum updated time in seconds] <dryRun>." . PHP_EOL;
	echo "example for looking at for %r74v1% pattern with files at minimum size of 100*1024*1024 and 2 days back use:" . PHP_EOL;
	echo "php deleteFilesFromEfs.php '%r74v1%' 104857600 172800 1" . PHP_EOL;
	die;
}


$minFileSize = $argv[1];
$efsFilePath = $argv[2];
$minUpdatedAtTime = $argv[3];

$dryRun = true;
if($argc > 4)
{
	$dryRun = $argv[4];
}

if ($dryRun)
{
	KalturaLog::info('*************** In Dry run mode ***************');
}
else
{
	KalturaLog::info('*************** In Real run mode ***************');
}

KalturaStatement::setDryRun($dryRun);
$deleteFilesWorker = new deleteFilesWorker($minFileSize, $efsFilePath, $minUpdatedAtTime, $dryRun);
$deleteFilesWorker->doWork();