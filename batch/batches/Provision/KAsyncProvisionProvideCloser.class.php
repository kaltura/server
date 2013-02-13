<?php
/**
 * Closes the process of provisioning a new stream.
 *
 * 
 * @package Scheduler
 * @subpackage Provision
 */
class KAsyncProvisionProvideCloser extends KJobCloserWorker
{
	/* (non-PHPdoc)
	 * @see KJobHandlerWorker::exec()
	 */
	protected function exec(KalturaBatchJob $job) {
		$this->closeProvisionProvide($job);
		
	}

	/* (non-PHPdoc)
	 * @see KBatchBase::getJobType()
	 */
	protected function getJobType() {
		return self::getType();
		
	}
	

	public static function getType()
	{
		return KalturaBatchJobType::PROVISION_PROVIDE;
	}

	protected function closeProvisionProvide (KalturaBatchJob $job)
	{
		$data = $job->data;
		/* @var $data KalturaAkamaiUniversalProvisionJobData */
		$primaryEntryPoint = parse_url($data->primaryBroadcastingUrl, PHP_URL_HOST);
		if (!$primaryEntryPoint)
		{
			return $this->closeJob($job, null, null, "Missing primary entry point", KalturaBatchJobStatus::FATAL);
		}
		
		if(($job->queueTime + $this->taskConfig->params->maxTimeBeforeFail) < time())
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CLOSER_TIMEOUT, 'Timed out', KalturaBatchJobStatus::FAILED);
		
		@exec("ping -w 1 $primaryEntryPoint", $output, $return);
		if ($return)
		{
			return $this->updateJob($job, "No reponse from primary entry point - retry in 5 mins", KalturaBatchJobStatus::ALMOST_DONE);
		}
		
		return $this->closeJob($job, null, null, 'Success', KalturaBatchJobStatus::FINISHED);
		
	}
	
}