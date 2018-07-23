<?php
class kReachJobsManager extends kJobsManager
{
	public static function addEntryVendorTasksCsvJob($partnerId, baseObjectFilter $filter, $kuser)
	{
		$jobData = new kEntryVendorTaskCsvJobData();
		$jobData->setFilter($filter);
		$jobData->setUserMail($kuser->getEmail());
		$jobData->setUserName($kuser->getPuserId());

		$batchJob = new BatchJob();
		$batchJob->setPartnerId($partnerId);
		$jobType = ReachPlugin::getBatchJobTypeCoreValue(ReachEntryVendorTasksCsvBatchType::ENTRY_VENDOR_TASK_CSV);

		return self::addJob($batchJob, $jobData, $jobType);
	}
}