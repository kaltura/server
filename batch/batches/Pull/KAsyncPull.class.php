<?php
require_once("bootstrap.php");
/**
 * Will import a single URL and store it in the file system.
 * The state machine of the job is as follows:
 * 	 	parse URL 
 * 		fetch heraders (to calculate the size of the file)
 * 		fetch file (update the job's progress - 100% is when the whole file as appeared in the header)
 * 		move the file to the archive 
 *
 * 
 * @package Scheduler
 * @subpackage Pull
 */
class KAsyncPull extends KBatchBase
{
	public static function getType()
	{
		return KalturaBatchJobType::PULL;
	}
	
	protected function init()
	{
		$this->saveQueueFilter(self::getType());
	}
	
	public function run()
	{
		KalturaLog::info("Pull batch is running");
	
		if($this->taskConfig->isInitOnly())
			return $this->init();
		
		$jobs = $this->kClient->batch->getExclusivePullJobs( 
			$this->getExclusiveLockKey(), 
			$this->taskConfig->maximumExecutionTime , 
			1 , 
			$this->getFilter());
			
		KalturaLog::info(count($jobs) . " pull jobs to perform");
								
		if(!count($jobs))
		{
			KalturaLog::info("Queue size: 0 sent to scheduler");
			$this->saveSchedulerQueue(self::getType());
			return;
		}
				
		foreach($jobs as $job)
			$this->fetchFile($job, $job->data);
	}
	
	/**
	 * Will take a single KalturaBatchJob and fetch the URL to the job's destFile
	 *  
	 * @param KalturaBatchJob $job
	 * @param KalturaPullJobData $data
	 */
	private function fetchFile ( KalturaBatchJob $job, KalturaPullJobData $data )
	{
		KalturaLog::debug("fetchFile($job->id)");
		
		try
		{
			$sourceUrl = $data->srcFileUrl;
			
			// TODO - control the path from config file
			// creates a temp file path 
			$content = myContentStorage::getFSContentRootPath();
			$uniqid = uniqid('import_');
			myContentStorage::fullMkdir("$content/content/imports/data");
			$destFile = "$content/content/imports/data/$uniqid";
			
			KalturaLog::debug("sourceUrl [$sourceUrl] destFile [$destFile]");
			
			// get the headers of the url - update the job  with the file size and status
			// TODO - get header ...
			
			$job->status = KalturaBatchJobStatus::QUEUED; 
			$job->message = 'Downloading file header';
			$job->data->destFileLocalPath = $destFile;
			$job->description = $job->message;
			$job->progress = "1" ; // just increment a little ;)
			$this->updatePullJob( $job ); 
			 
			// get the file 
			$curlWrapper = new KCurlWrapper ( $sourceUrl , $destFile );
			
			// update the job and the status of the entry
			$job->message = 'Downloading file';
			$job->description .= "\n" . $job->message;
			$job->progress = "2" ; // just increment a little more;)
			$this->updatePullJob( $job ); 
			
			$res = $curlWrapper->exec();
			
			if ( !$res )
			{
				// see what was the error !
				throw new exception ( $curlWrapper->getError() );
			}
						
			// see if the file can be copied to the archive
			$job->status = KalturaBatchJobStatus::MOVEFILE; 
			$job->message = 'Succesfully fetched file';
			$job->description .= "\n" . $job->message;
			$job->progress = "99" ; // just increment a little ;)
			$this->updatePullJob( $job ); 
			
			$job->status = KalturaBatchJobStatus::FINISHED; 
			$job->message = 'Succesfully moved file';
			$job->description .= "\n" . $job->message;
			$job->progress = "100" ; // just increment a little ;)			
			$this->updatePullJob( $job , KalturaEntryStatus::PRECONVERT );	// set the status of the entry to be KalturaEntryStatus::PRECONVERT
		}
		catch ( Exception $ex )
		{
			KalturaLog::err("Error: " . $ex->getMessage());
			
			// get the headers of the url - update the job  with the file size and status
			$job->status = KalturaBatchJobStatus::FAILED; 
			$job->message = 'FAILED';
			$job->description .= " \nError while pulling \n" . $ex->getMessage() ;
			$this->updatePullJob( $job , KalturaEntryStatus::ERROR_CONVERTING ); 	
		}
		
		$this->freePullJob($job);
	}
	
	protected function updateExclusiveJob($jobId, KalturaBatchJob $job, $entryStatus = null)
	{
		return $this->kClient->batch->updateExclusivePullJob($jobId, $this->getExclusiveLockKey(), $job, $entryStatus);
	}
	
	protected function freeExclusiveJob(KalturaBatchJob $job)
	{
		$resetExecutionAttempts = false;
		if($job->status == KalturaBatchJobStatus::ALMOST_DONE)
			$resetExecutionAttempts = true;
	
		$response = $this->kClient->batch->freeExclusivePullJob($job->id, $this->getExclusiveLockKey(), $resetExecutionAttempts);
		
		KalturaLog::info("Queue size: $response->queueSize sent to scheduler");
		$this->saveSchedulerQueue(self::getType(), $response->queueSize);
		
		return $response->job;
	}
}
?>