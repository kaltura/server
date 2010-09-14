<?php
interface kBatchJobStatusEventConsumer extends KalturaEventConsumer
{
	/**
	 * @param BatchJob $dbBatchJob
	 * @param int $entryStatus
	 * @param BatchJob $twinJob
	 * @return BatchJob
	 */
	public function updatedJob(BatchJob $dbBatchJob, $entryStatus, BatchJob $twinJob = null);
}