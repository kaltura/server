<?php

abstract class KStorageFileSyncsBase extends KPeriodicWorker
{
	const MAX_EXECUTION_TIME = 'maxExecutionTime';
	const SLEEP_INTERVAL = 'sleepInterval';
	const MAX_COUNT = 'maxCount';
	const FILE_SYNC_RESPONSE_PROFILE_FIELDS = 'id,originalId,fileSize,fileRoot,filePath,isDir,srcPath,srcEncKey,dc,storageClass';

	protected $storageProfiles;
	protected $fileSyncsToUpdate;

	protected $maxCount;

	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function run($jobs = null)
	{
		//Get Configuration
		$filter = $this->getFilter();
		$this->maxCount = $this->getAdditionalParams(self::MAX_COUNT);
		$sleepInterval = $this->getAdditionalParams(self::SLEEP_INTERVAL);
		$this->getParamsOperation();

		//Get All Storage Profiles
		$this->initStorageProfiles($filter->dcIn);
		if(!$this->storageProfiles)
		{
			KalturaLog::debug("No storage profiles to process");
			return;
		}

		$timeLimit = time() + $this->getAdditionalParams(self::MAX_EXECUTION_TIME);

		while (time() < $timeLimit)
		{
			//Get File Syncs
			$this->setPendingFileSyncResponseProfile();
			$lockResult = $this->lockFileSyncs($filter);

			if ($lockResult->fileSyncs)
			{
				//Process
				$this->processFileSyncs($lockResult->fileSyncs);

				//Update Status
				$this->setUpdateFileSyncResponseProfile();
				$this->updateFileSyncsStatus();
			}

			if (!$lockResult->limitReached)
			{
				sleep($sleepInterval);
			}
		}
	}

	protected function getParamsOperation()
	{

	}

	abstract protected function processOperation($engine);

	abstract protected function lockFileSyncs($filter);

	abstract protected function getOperationStatusSuccess();

	abstract protected function shouldUpdateOnError();

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

	protected function process($storageProfile, $fileSync)
	{
		$tempStorageExportData = new KalturaStorageExportJobData();
		$engine = KExportEngine::getInstance($storageProfile->protocol, $storageProfile->partnerId, $tempStorageExportData);
		if (!$engine)
		{
			KalturaLog::debug('Engine not found');
			return false;
		}
		$engine->setExportDataFields($storageProfile, $fileSync);

		$processResult = $this->processOperation($engine);
		if(!$processResult)
		{
			KalturaLog::debug('File process failed');
		}
		return $processResult;
	}

	protected function updateFileSyncsStatus()
	{
		$fileSyncPlugin = KalturaFileSyncClientPlugin::get(self::$kClient);
		self::$kClient->startMultiRequest();

		foreach ($this->fileSyncsToUpdate as $id => $status)
		{
			if( ($status != KalturaFileSyncStatus::ERROR) || $this->shouldUpdateOnError() )
			{
				$updateFileSync = new KalturaFileSync;
				$updateFileSync->status = $status;
				$fileSyncPlugin->fileSync->update($id, $updateFileSync);
			}
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

	protected function initStorageProfiles($dcs)
	{
		$storageProfileIds = explode(',', $dcs);
		if(!$storageProfileIds)
		{
			KalturaLog::debug('Storage Profile Ids are missing from config');
			return;
		}

		foreach ($storageProfileIds as $storageProfileId)
		{
			$this->setStorageProfileResponseProfile();
			$storageProfile = self::$kClient->storageProfile->get($storageProfileId);
			if (!$storageProfile)
			{
				KalturaLog::debug("Could not find storage Profile id [$storageProfileId]");
				continue;
			}

			KalturaLog::debug("Storing profile [$storageProfileId] info");
			$this->storageProfiles[$storageProfileId] = $storageProfile;
		}
	}

	protected function processFileSyncs($fileSyncs)
	{
		foreach ($fileSyncs as $fileSync)
		{
			if(!isset($this->storageProfiles[$fileSync->dc]))
			{
				KalturaLog::debug("No storage profile for storage ID [{$fileSync->dc}] file sync [{$fileSync->id}]");
				continue;
			}

			$storageProfile = $this->storageProfiles[$fileSync->dc];
			KalturaLog::debug("Process file sync id [{$fileSync->id}] storage id [{$storageProfile->id}]");

			$status = KalturaFileSyncStatus::ERROR;

			try
			{
				$processed = $this->process($storageProfile, $fileSync);
				if($processed)
				{
					$status = $this->getOperationStatusSuccess();
					KalturaLog::debug("File sync id [{$fileSync->id}] from storage id [{$storageProfile->id}] was processed. New status [{$status}]");
				}
			}
			catch(kApplicativeException $e)
			{
				$status = $this->handleAppException($e, $fileSync);
			}
			catch(Exception $e)
			{
				KalturaLog::err("Could not process file sync id [$fileSync->id]. Error: [{$e->getMessage()}]");
			}

			$this->fileSyncsToUpdate[$fileSync->id] = $status;
		}
	}

	protected function handleAppException($e, $fileSync)
	{
		KalturaLog::err("Could not process file sync id [$fileSync->id]. Error: [{$e->getMessage()}]");
		return KalturaFileSyncStatus::ERROR;
	}
}
