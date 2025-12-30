<?php
/**
 * Update bulk of UserEntry
 *
 * @package Scheduler
 * @subpackage BulkUpdateUserEntry
 */

class KAsyncBulkUpdateUserEntry extends KJobHandlerWorker
{
	/** (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::BULK_UPDATE_USER_ENTRY;
	}

	/**
	 * (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	protected function getJobType()
	{
		return KalturaBatchJobType::BULK_UPDATE_USER_ENTRY;
	}

	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		$jobData = $job->data;
		/** @var KalturaBulkUpdateUserEntryData $jobData */

		try
		{
			KBatchBase::impersonate($jobData->partnerId);
			$this->updateUserEntryStatus($jobData->partnerId, $jobData->entryId, $jobData->oldStatus, $jobData->newStatus);
			KBatchBase::unimpersonate();
		}
		catch (Exception $e)
		{
			KBatchBase::unimpersonate();
			return $this->closeJob($job, KalturaBatchJobErrorTypes::RUNTIME, $e->getCode(), "Error: " . $e->getMessage(), KalturaBatchJobStatus::FAILED, $jobData);
		}

		return $job;
	}

	protected function updateUserEntryStatus($partnerId, $entryId, $oldStatus, $newStatus)
	{
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		$pager->pageIndex = 1;

		$filter = new KalturaUserEntryFilter();
		$filter->partnerId = $partnerId;
		$filter->entryIdEqual = $entryId;
		$filter->statusEqual = $oldStatus;

		do
		{
			$userEntryList = KBatchBase::$kClient->userEntry->listAction($filter, $pager);
			foreach ($userEntryList->objects as $userEntry)
			{
				/** @var KalturaUserEntry $userEntry */
				$class = get_class($userEntry);
				$updatedUserEntry = new $class();
				$updatedUserEntry->status = $newStatus;

				KBatchBase::$kClient->userEntry->update($userEntry->id, $updatedUserEntry);
			}
		} while (count($userEntryList->objects) === $pager->pageSize);
	}
}
