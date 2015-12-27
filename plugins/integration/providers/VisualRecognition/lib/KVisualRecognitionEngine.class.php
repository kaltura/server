<?php
/**
 * @package plugins.visualRecognition
 * @subpackage Scheduler
 */
class KVisualRecognitionEngine implements KIntegrationCloserEngine
{	
	/* (non-PHPdoc)
	 * @see KIntegrationCloserEngine::dispatch()
	 */
	public function dispatch(KalturaBatchJob $job, KalturaIntegrationJobData &$data)
	{
		return $this->doDispatch($job, $data, $data->providerData);
	}

	/* (non-PHPdoc)
	 * @see KIntegrationCloserEngine::close()
	 */
	public function close(KalturaBatchJob $job, KalturaIntegrationJobData &$data)
	{
		return $this->doClose($job, $data, $data->providerData);
	}
	
	protected function doDispatch(KalturaBatchJob $job, KalturaIntegrationJobData &$data, KalturaVisualRecognitionJobProviderData $providerData)
	{
KalturaLog::crit("gonen, writing to log is fun!");
		KalturaLog::crit("Thumbnail interval [$providerData->thumbInterval]");
                if(!empty($job->entryId))
                {
                        KBatchBase::impersonate($job->partnerId);
                        $entry = KBatchBase::$kClient->baseEntry->get($job->entryId);
                        KBatchBase::unimpersonate();
                        if($entry instanceof KalturaMediaEntry)
                        {
                                KalturaLog::crit("gonen: entry is media, has duration of ".$entry->duration);
                                KalturaLog::crit("gonen: thumbmail URL is ".$entry->thumbnailUrl);
                        }
                }

KalturaLog::crit("gonen priting the job data");
KalturaLog::crit(print_r($data, true));
$val = new KalturaKeyValue();
$val->key = "vid_sec_4";
$val->value = "job_id_97872";
$data->providerData->externalJobs = array($val);
KalturaLog::crit(print_r($data, true));
		// To finish, return true
		// To wait for closer, return false
		// To fail, throw exception
		
		return false;
	}
	
	protected function doClose(KalturaBatchJob $job, KalturaIntegrationJobData &$data, KalturaVisualRecognitionJobProviderData $providerData)
	{
		KalturaLog::debug("Thumbnail interval [$providerData->thumbInterval]");

		KalturaLog::crit("gonen priting the job data");
KalturaLog::crit(print_r($data, true));

		// To finish, return true
		// To keep open for future closer, return false
		// To fail, throw exception
		
		return true;
	}
}
