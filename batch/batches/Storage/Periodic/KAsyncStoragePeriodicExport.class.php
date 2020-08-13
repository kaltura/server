<?php

/**
 * process remote storage files and export using file handlers as required
 *
 * @package Scheduler
 * @subpackage Storage
 */
class KAsyncStoragePeriodicExport extends KStorageFileSyncsBase
{
	const MAX_SIZE = 'maxSize';

	protected $maxSize;
	protected $currentIndex;

	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::STORAGE_PERIODIC_EXPORT;
	}

	protected function getParamsOperation()
	{
		$this->maxSize = $this->getAdditionalParams(self::MAX_SIZE);
	}

	protected function lockFileSyncs($filter)
	{
		// Get storage profile
		$storageProfile = current($this->storageProfiles);

		if(!next($this->storageProfiles))
		{
			reset($this->storageProfiles);
		}

		if($filter->createdAt && $filter->createdAt <0)
        {
            $filter->createdAt = now() + $filter->createdAt;
        }

        // Update filter
        $filter->dcIn = null;
        $filter->dcEqual = $storageProfile->id;

        if(isset($filter->createdAt) && $filter->createdAt <0)
        {
            $filter->createdAt = time() + $filter->createdAt;
        }


        KalturaLog::debug("lock pending file syncs with dc [$storageProfile->id]");

		return self::$kClient->storageProfile->lockPendingFileSyncs($filter, $this->getId(), $storageProfile->id, $this->maxCount, $this->maxSize);
	}

	protected function processOperation($engine)
	{
		return $engine->export();
	}

	protected function getOperationStatusSuccess()
	{
		return KalturaFileSyncStatus::READY;
	}

	protected function shouldUpdateOnError()
	{
		return true;
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
		return $status;
	}
}