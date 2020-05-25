<?php

/**
 * process remote storage files and delete using file handlers as required
 *
 * @package Scheduler
 * @subpackage Storage
 */
class KAsyncStoragePeriodicPurge extends KStorageFileSyncsBase
{
	const LOCK_EXPIRY_TIMEOUT = 'lockExpiryTimeout';
	const RELATIVE_TIME_RANGE = 'relativeTimeRange';
	const RELATIVE_TIME_DELETION_LIMIT = 'relativeTimeDeletionLimit';

	protected $lockExpiryTimeout;
	protected $relativeTimeRange;
	protected $relativeTimeDeletionLimit;

	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::STORAGE_PERIODIC_PURGE;
	}

	protected function getParamsOperation()
	{
		$this->lockExpiryTimeout = $this->getAdditionalParams(self::LOCK_EXPIRY_TIMEOUT);
		$this->relativeTimeRange = $this->getAdditionalParams(self::RELATIVE_TIME_RANGE);
		$this->relativeTimeDeletionLimit = $this->getAdditionalParams(self::RELATIVE_TIME_DELETION_LIMIT);
	}

	protected function lockFileSyncs($filter)
	{
		return self::$kClient->fileSync->lockFileSyncs($filter, $this->getId(), $this->relativeTimeDeletionLimit,
			$this->relativeTimeRange, $this->lockExpiryTimeout, $this->maxCount);
	}

	protected function processOperation($engine)
	{
		return $engine->delete();
	}

	protected function getOperationStatusSuccess()
	{
		return KalturaFileSyncStatus::PURGED;
	}

	protected function shouldUpdateOnError()
	{
		return false;
	}
}