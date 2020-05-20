<?php

/**
 * process remote storage files and delete local file syncs
 *
 * @package Scheduler
 * @subpackage Storage
 */
class KAsyncStoragePeriodicDeleteLocal extends KPeriodicWorker
{
	const MAX_EXECUTION_TIME = 'maxExecutionTime';        // can be exceeded by one file sync
	const IDLE_SLEEP_INTERVAL = 'sleepInterval';
	const LOCK_EXPIRY_TIMEOUT = 'lockExpiryTimeout';
	const GAP = 'gap';
	const RANGE = 'range';

	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::STORAGE_PERIODIC_DELETE_LOCAL;
	}

	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function run($jobs = null)
	{
		$filter = $this->getFilter();
		$sleepInterval = $this->getAdditionalParams(self::IDLE_SLEEP_INTERVAL);
		$gap = $this->getAdditionalParams(self::GAP);
		$range = $this->getAdditionalParams(self::RANGE);
		$lockExpiryTimeout = $this->getAdditionalParams(self::LOCK_EXPIRY_TIMEOUT);

		$timeLimit = time() + $this->getAdditionalParams(self::MAX_EXECUTION_TIME);
		while (time() < $timeLimit)
		{
			$this->setDeleteLocalFileSyncsResponseProfile();
			self::$kClient->fileSync->deleteLocalFileSyncs($filter, $this->getId(), $gap, $range, $lockExpiryTimeout);

			sleep($sleepInterval);
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

	protected function setDeleteLocalFileSyncsResponseProfile()
	{
		$responseProfile = new KalturaDetachedResponseProfile();
		$responseProfile->type = KalturaResponseProfileType::INCLUDE_FIELDS;
		$responseProfile->fields = 'id';        // don't need the response
		self::$kClient->setResponseProfile($responseProfile);
	}

}