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
		$backupEntryPoint = parse_url($data->secondaryBroadcastingUrl, PHP_URL_HOST);
		if (!$primaryEntryPoint || !$backupEntryPoint)
		{
			return $this->closeJob($job, null, null, "Missing one or both entry points", KalturaBatchJobStatus::FATAL);
		}
		
		if(($job->queueTime + $this->taskConfig->params->maxTimeBeforeFail) < time())
			return $this->closeJob($job, KalturaBatchJobErrorTypes::APP, KalturaBatchJobAppErrors::CLOSER_TIMEOUT, 'Timed out', KalturaBatchJobStatus::FAILED);
		
		$pingTimeout = $this->taskConfig->params->pingTimeout;
		@exec("ping -w $pingTimeout $primaryEntryPoint", $output, $return);
		if ($return)
		{
			return $this->closeJob($job, "No reponse from primary entry point - retry in 5 mins", KalturaBatchJobStatus::ALMOST_DONE);
		}
		
		@exec("ping -w $pingTimeout $backupEntryPoint", $output, $return);
		if ($return)
		{
			return $this->closeJob($job, "No reponse from backup entry point - retry in 5 mins", KalturaBatchJobStatus::ALMOST_DONE);
		}
		
		return $this->closeJob($job, null, null, 'Success', KalturaBatchJobStatus::FINISHED);
		
	}
	
}