<?php

class BulkUploadResultJob extends BulkUploadResult
{
	
	const JOB_OBJECT_ID = 'JOB_OBJECT_ID';
	
	/**
	 * @return mixed
	 */
	public function getJobId()
	{
		return $this->getFromCustomData(self::JOB_OBJECT_ID);
	}
	
	/**
	 * @param mixed $jobId
	 */
	public function setJobId($jobId)
	{
		$this->putInCustomData(self::JOB_OBJECT_ID, $jobId);
	}
	
	public function updateStatusFromObject ()
	{
		$job = BatchJobPeer::retrieveByPK($this->getObjectId());
		if(!$job)
			return $this->getStatus();
		
		$this->setObjectStatus($job->getStatus());
		
		$closedStatuses = array (
			BatchJob::BATCHJOB_STATUS_FINISHED,
		);
		
		$errorStatuses = array (
			BatchJob::BATCHJOB_STATUS_FAILED,
			BatchJob::BATCHJOB_STATUS_FATAL,
			BatchJob::BATCHJOB_STATUS_ABORTED,
		);
		
		if(in_array($this->getObjectStatus(), $closedStatuses))
		{
			$this->setStatus(BulkUploadResultStatus::OK);
			$this->save();
		}
		else if (in_array($this->getObjectStatus(), $errorStatuses))
		{
			$this->setStatus(BulkUploadResultStatus::ERROR);
			$this->save();
		}
		
		return $this->getStatus();
	}
	
}