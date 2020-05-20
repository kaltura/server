<?php

/**
 * process remote storage files and export using file handlers as required
 *
 * @package Scheduler
 * @subpackage Storage
 */
class KAsyncStoragePeriodicExport extends KPeriodicWorker
{
	const MAX_EXECUTION_TIME = 'maxExecutionTime';        // can be exceeded by one file sync
	const IDLE_SLEEP_INTERVAL = 'sleepInterval';
	const MAX_COUNT = 'maxCount';
	const MAX_SIZE = 'maxSize';
	const STORAGE_PROFILE_IDS = 'periodic_storage_ids';
	const FILE_SYNC_RESPONSE_PROFILE_FIELDS = 'id,originalId,fileSize,fileRoot,filePath,isDir,srcPath,srcEncKey';

	protected $storageProfiles;
	protected $currentIndex;
	protected $fileSyncsToUpdate;
	protected $storageProfileIdsArray;

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
		$sleepInterval = $this->getAdditionalParams(self::IDLE_SLEEP_INTERVAL);
		$this->storageProfileIdsArray = self::getConfigParam(self::STORAGE_PROFILE_IDS, 'cloud_storage', null);
		if(!$this->storageProfileIdsArray)
		{
			KalturaLog::debug('Storage Profile Ids are missing from config');
			return;
		}
		$this->currentIndex = 0;
		$timeLimit = time() + $this->getAdditionalParams(self::MAX_EXECUTION_TIME);

		while (time() < $timeLimit)
		{
			$this->setStorageProfileResponseProfile();
			$storageProfile = $this->getStorageProfile();
			if(!$storageProfile)
			{
				continue;
			}

			$this->setPendingFileSyncResponseProfile();
			$lockResult = self::$kClient->storageProfile->lockPendingFileSyncs($filter, $this->getId(), $storageProfile->id, $maxCount, $maxSize);
			if (!$lockResult->fileSyncs)
			{
				sleep($sleepInterval);
				continue;
			}

			$this->exportFileSyncs($lockResult->fileSyncs, $storageProfile);

			$this->setUpdateFileSyncResponseProfile();
			$this->updateFileSyncsStatus();

			if (!$lockResult->limitReached)
			{
				sleep($sleepInterval);
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
		$tempStorageExportData = new KalturaStorageExportJobData();
		$engine = KExportEngine::getInstance($storageProfile->protocol, $storageProfile->partnerId, $tempStorageExportData);
		if (!$engine)
		{
			KalturaLog::debug('Engine not found');
			return false;
		}
		$engine->setExportDataFields($storageProfile, $fileSync);

		$exportResult = $engine->export();
		if(!$exportResult)
		{
			KalturaLog::debug('File export failed');
		}
		return $exportResult;
	}

	protected function updateFileSyncsStatus()
	{
		$fileSyncPlugin = KalturaFileSyncClientPlugin::get(self::$kClient);
		self::$kClient->startMultiRequest();

		foreach ($this->fileSyncsToUpdate as $id => $status)
		{
			$updateFileSync = new KalturaFileSync;
			$updateFileSync->status = $status;
			$fileSyncPlugin->fileSync->update($id, $updateFileSync);
		}

		try
		{
			self::$kClient->doMultiRequest();
			$this->fileSyncsToUpdate = array();
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

	protected function setStorageProfileResponseProfile()
	{
		$responseProfile = new KalturaDetachedResponseProfile();
		self::$kClient->setResponseProfile($responseProfile);
	}

	protected function setPendingFileSyncResponseProfile()
	{
		$responseProfile = new KalturaDetachedResponseProfile();
		$responseProfile->type = KalturaResponseProfileType::INCLUDE_FIELDS;
		$responseProfile->fields = self::FILE_SYNC_RESPONSE_PROFILE_FIELDS;
		self::$kClient->setResponseProfile($responseProfile);
	}

	protected function setUpdateFileSyncResponseProfile()
	{
		$responseProfile = new KalturaDetachedResponseProfile();
		$responseProfile->type = KalturaResponseProfileType::INCLUDE_FIELDS;
		$responseProfile->fields = 'id';        // don't need the response
		self::$kClient->setResponseProfile($responseProfile);
	}

	protected function getStorageProfile()
	{
		$storageProfileIdsArray = $this->storageProfileIdsArray;
		if($this->currentIndex >= count($storageProfileIdsArray))
		{
			$this->currentIndex = 0;
		}

		if(isset($this->storageProfiles[$this->currentIndex]))
		{
			$index = $this->currentIndex;
			$id = $this->storageProfiles[$index]->id;
			KalturaLog::debug("Using Storing profile [$id], index [$this->currentIndex]");
			$this->currentIndex++;
			return $this->storageProfiles[$index];
		}

		$storageProfileId = $storageProfileIdsArray[$this->currentIndex];

		$storageProfile = self::$kClient->storageProfile->get($storageProfileId);
		if (!$storageProfile)
		{
			KalturaLog::debug("Could not find storage Profile id [$storageProfileId]");
		}

		KalturaLog::debug("Storing profile [$storageProfileId] info, index [$this->currentIndex]");
		$this->storageProfiles[$this->currentIndex] = $storageProfile;
		$this->currentIndex++;
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
				$this->fileSyncsToUpdate[$fileSync->id] = $status;
			}
			catch(kApplicativeException $e)
			{
				$this->handleAppException($e, $fileSync);
			}
			catch(Exception $e)
			{
				KalturaLog::err("Could not export file. Error: [{$e->getMessage()}]");
				$this->fileSyncsToUpdate[$fileSync->id] = KalturaFileSyncStatus::ERROR;
			}
		}
	}

	protected function handleAppException($e, $fileSync)
	{
		if($e->getCode() == KalturaBatchJobAppErrors::FILE_ALREADY_EXISTS)
		{
			KalturaLog::debug("File with path [$fileSync->filePath}] already exists in remote storage");
			$status = KalturaFileSyncStatus::READY;
		}
		else
		{
			KalturaLog::err("Could not export file sync id [$fileSync->id]. Error: [{$e->getMessage()}]");
			$status = KalturaFileSyncStatus::ERROR;
		}
		$this->fileSyncsToUpdate[$fileSync->id] = $status;
	}
}