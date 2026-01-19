<?php
/**
 * Update UserEntries
 *
 * @package Scheduler
 * @subpackage UpdateUserEntries
 */

class KAsyncUpdateUserEntries extends KJobHandlerWorker
{
	/** (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::UPDATE_USER_ENTRIES;
	}

	/**
	 * (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	protected function getJobType()
	{
		return KalturaBatchJobType::UPDATE_USER_ENTRIES;
	}

	/**
	 * (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		$jobData = $job->data;
		/** @var KalturaUpdateUserEntriesData $jobData */

		try
		{
			KBatchBase::impersonate($job->partnerId);
			$this->updateUserEntryStatus($job->partnerId, $job->entryId, $jobData->oldStatus, $jobData->newStatus);
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
			try
			{
				$userEntryList = KBatchBase::$kClient->userEntry->listAction($filter, $pager);
			}
			catch (KalturaAPIException $e)
			{
				KalturaLog::err("Failed to list user entries for entryId [$entryId]: " . $e->getMessage());
				throw $e;
			}
			foreach ($userEntryList->objects as $userEntry)
			{
				/** @var KalturaUserEntry $userEntry */
				$class = get_class($userEntry);
				$updatedUserEntry = new $class();
				$updatedUserEntry->status = $newStatus;

				try
				{
					KBatchBase::$kClient->userEntry->update($userEntry->id, $updatedUserEntry);
				}
				catch (KalturaAPIException $e)
				{
					KalturaLog::err("Failed to update user entry [{$userEntry->id}] status from [$oldStatus] to [$newStatus]: " . $e->getMessage());
					throw $e;
				}
			}
		} while (count($userEntryList->objects) === $pager->pageSize);
	}
}
