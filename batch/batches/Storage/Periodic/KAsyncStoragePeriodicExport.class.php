<?php

/**
 * process remote storage files and export using file handlers as required
 *
 * @package Scheduler
 * @subpackage Storage
 */
class KAsyncStoragePeriodicExport extends KPeriodicWorker
{
	const MAX_EXECUTION_TIME = 3600;        // can be exceeded by one file sync
	const IDLE_SLEEP_INTERVAL = 10;
	const MAX_COUNT = 'maxCount';
	const MAX_SIZE = 'maxSize';
	const STORAGE_PROFILE_IDS = 'profileIdsIn';
	const RESPONSE_PROFILE_FIELDS = 'id,originalId,fileSize,fileRoot,filePath,isDir,srcPath,srcEncKey';

	protected $storageProfileIdsArray;
	protected $currentIndex;

	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::STORAGE_PERIODIC_EXPORT;
	}

	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function run($jobs = null)
	{
		$filter = $this->getFilter();
		$maxCount = $this->getAdditionalParams(self::MAX_COUNT);
		$maxSize = $this->getAdditionalParams(self::MAX_SIZE);
		$this->storageProfileIdsArray = explode(',', $this->getAdditionalParams(self::STORAGE_PROFILE_IDS));
		if(!$this->storageProfileIdsArray)
		{
			KalturaLog::debug('Storage Profile Ids are missing from config');
			return;
		}
		$this->currentIndex = 0;
		$responseProfile = $this->initResponseProfile();
		$timeLimit = time() + self::MAX_EXECUTION_TIME;

		while (time() < $timeLimit)
		{
			$storageProfile = $this->getStorageProfile();
			if(!$storageProfile)
			{
				continue;
			}

			self::$kClient->setResponseProfile($responseProfile);
			$lockResult = self::$kClient->storageProfile->lockPendingFileSyncs($filter, $this->getId(), $storageProfile->id, $maxCount, $maxSize);
			if (!$lockResult->fileSyncs)
			{
				sleep(self::IDLE_SLEEP_INTERVAL);
				continue;
			}

			$this->exportFileSyncs($lockResult->fileSyncs, $storageProfile);

			if (!$lockResult->limitReached)
			{
				sleep(self::IDLE_SLEEP_INTERVAL);
			}
		}
	}

	protected function getFilter()
	{
		$filter = new KalturaFileSyncFilter();
		if (KBatchBase::$taskConfig->filter)
		{
			// copy the attributes since KBatchBase::$taskConfig->filter is of type KalturaBatchJobFilter
			foreach (KBatchBase::$taskConfig->filter as $attr => $value)
			{
				$filter->$attr = $value;
			}
		}
		return $filter;
	}

	protected function export($storageProfile, $fileSync)
	{
		$data = $this->createExportData($storageProfile, $fileSync);
		$engine = KExportEngine::getInstance($storageProfile->protocol, $storageProfile->partnerId, $data);
		if (!$engine)
		{
			KalturaLog::debug('Engine not found');
			return false;
		}

		$exportResult = $engine->export();
		if(!$exportResult)
		{
			KalturaLog::debug('File export failed');
		}
		return $exportResult;
	}


	protected function createExportData($storageProfile, $fileSync)
	{
		if ($storageProfile->protocol == StorageProfileProtocol::S3)
		{
			$storageExportData = new KalturaAmazonS3StorageExportJobData();
		}
		else
		{
			$storageExportData = new KalturaStorageExportJobData();
		}

		$storageExportData = $this->fillStorageExportJobData($storageExportData, $storageProfile, $fileSync);
		return $storageExportData;
	}

	protected function changeFileSyncStatus(KalturaFileSync $fileSync, $status)
	{
		$updateFileSync = new KalturaFileSync;
		$updateFileSync->status = $status;

		try
		{
			$responseProfile = new KalturaDetachedResponseProfile();
			$responseProfile->type = KalturaResponseProfileType::INCLUDE_FIELDS;
			$responseProfile->fields = '';        // don't need the response
			self::$kClient->setResponseProfile($responseProfile);

			$fileSyncPlugin = KalturaFileSyncClientPlugin::get(self::$kClient);
			$fileSyncPlugin->fileSync->update($fileSync->id, $updateFileSync);
		}
		catch (KalturaException $e)
		{
			KalturaLog::err($e);
		}
		catch (KalturaClientException $e)
		{
			KalturaLog::err($e);
		}
	}

	protected function fillStorageExportJobData($storageExportData, $externalStorage, $fileSync, $force = false)
	{
		$storageExportData->serverUrl = $externalStorage->storageUrl;
		$storageExportData->serverUsername = $externalStorage->storageUsername;
		$storageExportData->serverPassword = $externalStorage->storagePassword;
		$storageExportData->serverPrivateKey = $externalStorage->privateKey;
		$storageExportData->serverPublicKey = $externalStorage->publicKey;
		$storageExportData->serverPassPhrase = $externalStorage->passPhrase;
		$storageExportData->ftpPassiveMode = $externalStorage->storageFtpPassiveMode;

		$storageExportData->srcFileSyncLocalPath = $fileSync->srcPath;
		$storageExportData->srcFileEncryptionKey = $fileSync->srcEncKey;
		$storageExportData->srcFileSyncId = $fileSync->id;

		$storageExportData->force = $force;
		$storageExportData->destFileSyncStoredPath = $externalStorage->storageBaseDir . '/' . $fileSync->filePath;
		$storageExportData->createLink = $externalStorage->createFileLink;

		if($externalStorage->protocol == StorageProfileProtocol::S3)
		{
			$storageExportData = $this->addS3FieldsToStorageData($storageExportData, $externalStorage);
		}

		return $storageExportData;
	}

	protected function addS3FieldsToStorageData($storageExportData, $externalStorage)
	{
		$storageExportData->filesPermissionInS3 = $externalStorage->filesPermissionInS3;
		$storageExportData->s3Region = $externalStorage->s3Region;
		$storageExportData->sseType = $externalStorage->sseType;
		$storageExportData->sseKmsKeyId = $externalStorage->sseKmsKeyId;
		$storageExportData->signatureType = $externalStorage->signatureType;
		return $storageExportData;
	}

	protected function initResponseProfile()
	{
		$responseProfile = new KalturaDetachedResponseProfile();
		$responseProfile->type = KalturaResponseProfileType::INCLUDE_FIELDS;
		$responseProfile->fields = self::RESPONSE_PROFILE_FIELDS;
		return $responseProfile;
	}

	protected function getStorageProfile()
	{
		$responseProfile = new KalturaDetachedResponseProfile();
		self::$kClient->setResponseProfile($responseProfile);
		KBatchBase::impersonate(0);
		if($this->currentIndex >= count($this->storageProfileIdsArray))
		{
			$this->currentIndex = 0;
		}
		$storageProfileId = $this->storageProfileIdsArray[$this->currentIndex];
		$this->currentIndex++;

		$storageProfile = self::$kClient->storageProfile->get($storageProfileId);
		if (!$storageProfile)
		{
			KalturaLog::debug("Could not find storage Profile id [$storageProfileId]");
		}
		KBatchBase::unimpersonate();
		return $storageProfile;
	}

	protected function exportFileSyncs($fileSyncs, $storageProfile)
	{
		foreach ($fileSyncs as $fileSync)
		{
			KalturaLog::debug("Export file sync id [{$fileSync->id}] to storage id [{$storageProfile->id}]");
			try
			{
				$exported = $this->export($storageProfile, $fileSync);
				if($exported)
				{
					$status = KalturaFileSyncStatus::READY;
				}
				else
				{
					$status = KalturaFileSyncStatus::ERROR;
				}
				$this->changeFileSyncStatus($fileSync, $status);
			}
			catch(kApplicativeException $e)
			{
				$this->handleAppException($e, $fileSync);
				break;
			}
			catch(Exception $e)
			{
				KalturaLog::err("Could not export file. Error: [{$e->getMessage()}]");
				$this->changeFileSyncStatus($fileSync, KalturaFileSyncStatus::ERROR);
			}
		}
	}

	protected function handleAppException($e, $fileSync)
	{
		if($e->getCode() == KalturaBatchJobAppErrors::FILE_ALREADY_EXISTS)
		{
			KalturaLog::debug("File already exists in remote storage");
			$status = KalturaFileSyncStatus::READY;
		}
		else
		{
			KalturaLog::err("Could not export file. Error: [{$e->getMessage()}]");
			$status = KalturaFileSyncStatus::ERROR;
		}
		$this->changeFileSyncStatus($fileSync, $status);
	}
}