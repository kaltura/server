<?php
interface kBatchJobStatusEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param BatchJob $dbBatchJob
	 * @param int $entryStatus
	 * @param BatchJob $twinJob
	 * @return bool true if should continue to the next consumer
	 */
	public function updatedJob(BatchJob $dbBatchJob, $entryStatus, BatchJob $twinJob = null);
}