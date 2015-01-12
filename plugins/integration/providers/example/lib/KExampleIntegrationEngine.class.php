<?php
/**
 * @package plugins.exampleIntegration
 * @subpackage Scheduler
 */
class KExampleIntegrationEngine implements KIntegrationCloserEngine
{	
	/* (non-PHPdoc)
	 * @see KIntegrationCloserEngine::dispatch()
	 */
	public function dispatch(KalturaBatchJob $job, KalturaIntegrationJobData &$data)
	{
		$this->doDispatch($job, $data, $data->providerData);
	}

	/* (non-PHPdoc)
	 * @see KIntegrationCloserEngine::close()
	 */
	public function close(KalturaBatchJob $job, KalturaIntegrationJobData $data)
	{
		$this->doClose($job, $data, $data->providerData);
	}
	
	protected function doDispatch(KalturaBatchJob $job, KalturaIntegrationJobData &$data, KalturaExampleIntegrationJobProviderData $providerData)
	{
		KalturaLog::debug("Example URL [$providerData->exampleUrl]");
		
		// To finish, return true
		// To wait for closer, return false
		// To fail, throw exception
		
		return true;
	}
	
	protected function doClose(KalturaBatchJob $job, KalturaIntegrationJobData &$data, KalturaExampleIntegrationJobProviderData $providerData)
	{
		KalturaLog::debug("Example URL [$providerData->exampleUrl]");
		
		// To finish, return true
		// To keep open for future closer, return false
		// To fail, throw exception
		
		return true;
	}
}
