<?php

/**
 * process remote storage files and delete using file handlers as required
 *
 * @package Scheduler
 * @subpackage Storage
 */
class KAsyncStoragePeriodicPurge extends KStorageFileSyncsBase
{
	const GAP = 'gap';
	const RELATIVE_TIME_RANGE = 'relativeTimeRange';
	const LOCK_EXPIRY_TIMEOUT = 'lockExpiryTimeout';

	protected $gap;
	protected $relativeTimeRange;
	protected $lockExpiryTimeout;

	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::STORAGE_PERIODIC_PURGE;
	}

	protected function getParamsOperation()
	{
		$this->gap = $this->getAdditionalParams(self::GAP);
		$this->relativeTimeRange = $this->getAdditionalParams(self::RELATIVE_TIME_RANGE);
		$this->lockExpiryTimeout = $this->getAdditionalParams(self::LOCK_EXPIRY_TIMEOUT);
	}

	protected function lockFileSyncs($filter)
	{
		$filter->updatedAtLessThanOrEqual = time() - $this->gap;
		$filter->updatedAtGreaterThanOrEqual = $filter->updatedAtLessThanOrEqual - $this->relativeTimeRange;
		return self::$kClient->fileSync->lockFileSyncs($filter, $this->maxCount, $this->lockExpiryTimeout);
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