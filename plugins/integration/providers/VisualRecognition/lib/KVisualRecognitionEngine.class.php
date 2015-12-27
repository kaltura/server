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
		KalturaLog::debug("Recognize URL [$providerData->recognizeElementURL]");
		
		// To finish, return true
		// To wait for closer, return false
		// To fail, throw exception
		
		return false;
	}
	
	protected function doClose(KalturaBatchJob $job, KalturaIntegrationJobData &$data, KalturaVisualRecognitionJobProviderData $providerData)
	{
		KalturaLog::debug("Recognize URL [$providerData->recognizeElementURL]");
		
		// To finish, return true
		// To keep open for future closer, return false
		// To fail, throw exception
		
		return true;
	}
}
