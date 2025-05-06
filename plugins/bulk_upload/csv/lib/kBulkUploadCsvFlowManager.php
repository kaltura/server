<?php
/**
 * @package plugins.bulkUploadCsv
 * @subpackage lib
 */

class kBulkUploadCsvFlowManager implements kBatchJobStatusEventConsumer
{
	public function updatedJob(BatchJob $dbBatchJob)
	{
		$jobData = $dbBatchJob->getData();
		if ($jobData instanceof kBulkUploadCsvJobData)
		{
			$objectType = $jobData->getProcessObjectType();
			if($objectType === kBulkGroupUsersToGroupCsv::GROUP_ID)
			{
				$objectId = $jobData->getProcessObjectId();
				$kGroup = kuserPeer::getKuserByPartnerAndUid($dbBatchJob->getPartnerId(), $objectId);
				if($kGroup)
				{
					$kGroup->setProcessStatus(GroupProcessStatus::NONE);
					$kGroup->save();
				}
			}
		}

		return true;
	}

	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		if($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED &&
			$dbBatchJob->getJobType() == BatchJobType::BULKUPLOAD &&
			$dbBatchJob->getJobSubType() == BulkUploadCsvPlugin::getBulkUploadTypeCoreValue(BulkUploadCsvType::CSV))
		{
			return true;
		}
		return false;
	}
}
