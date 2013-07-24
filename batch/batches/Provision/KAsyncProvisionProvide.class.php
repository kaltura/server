<?php
/**
 * Will provision new live stream.
 *
 * 
 * @package Scheduler
 * @subpackage Provision
 */
class KAsyncProvisionProvide extends KJobHandlerWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::PROVISION_PROVIDE;
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	public function getJobType()
	{
		return self::getType();
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job)
	{
		return $this->provision($job, $job->data);
	}
	
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::getMaxJobsEachRun()
	 */
	protected function getMaxJobsEachRun()
	{
		return 1;
	}
	
	protected function provision(KalturaBatchJob $job, KalturaProvisionJobData $data)
	{
		KalturaLog::notice ( "Provision entry");
		
		$job = $this->updateJob($job, null, KalturaBatchJobStatus::QUEUED);
		
		$engine = KProvisionEngine::getInstance( $job->jobSubType , $data);
		
		if ( $engine == null )
		{
			$err = "Cannot find provision engine [{$job->jobSubType}] for job id [{$job->id}]";
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::ENGINE_NOT_FOUND, $err, KalturaBatchJobStatus::FAILED);
		}
		
		KalturaLog::info( "Using engine: " . $engine->getName() );
	
		$results = $engine->provide($job, $data);

		if($results->status == KalturaBatchJobStatus::FINISHED)
			return $this->closeJob($job, null, null, null, KalturaBatchJobStatus::ALMOST_DONE, $results->data);
			
		return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, null, $results->errMessage, $results->status, $results->data);
	}
}
