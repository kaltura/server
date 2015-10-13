<?php
/**
 * @package Scheduler
 * @subpackage Bulk-Upload
 */

/**
 * Will close almost done bulk uploads.
 * The state machine of the job is as follows:
 * 	 	get almost done bulk uploads 
 * 		check the imports and converts statuses
 * 		update the bulk status
 *
 * @package Scheduler
 * @subpackage Bulk-Upload
 */
class KAsyncBulkUploadCloser extends KJobCloserWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::BULKUPLOAD;
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->fetchStatus($job);
	}
	
	private function fetchStatus(KalturaBatchJob $job)
	{
		if(($job->queueTime + self::$taskConfig->params->maxTimeBeforeFail) < time())
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CLOSER_TIMEOUT, 'Timed out', KalturaBatchJobStatus::FAILED);
			
		$openedEntries = self::$kClient->batch->updateBulkUploadResults($job->id);
		$job = $this->updateJob($job, "Unclosed entries remaining: $openedEntries" , KalturaBatchJobStatus::ALMOST_DONE);
		if(!$openedEntries)
		{
		    $numOfObjects = $job->data->numOfObjects;
		    $numOfErrorObjects = $job->data->numOfErrorObjects;
		    KalturaLog::info("numOfSuccessObjects: $numOfObjects, numOfErrorObjects: $numOfErrorObjects");
		    
		    if ($numOfErrorObjects == 0)
		    {
			    return $this->closeJob($job, null, null, 'Finished successfully', KalturaBatchJobStatus::FINISHED);
		    }
		    else if($numOfObjects > 0) //some objects created successfully
		    {
		    	return $this->closeJob($job, null, null, 'Finished, but with some errors', KalturaBatchJobStatus::FINISHED_PARTIALLY);
		    }
		    else
		    {
		        return $this->closeJob($job, null, null, 'Failed to create objects', KalturaBatchJobStatus::FAILED);
		    }
		}	
		return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::ALMOST_DONE);
	}
}
