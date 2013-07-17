<?php

class KAsyncWidevineRepositorySync extends KJobHandlerWorker
{	
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::WIDEVINE_REPOSITORY_SYNC;
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
		return $this->syncRepository($job, $job->data);			
	}

	protected function syncRepository(KalturaBatchJob $job, KalturaWidevineRepositorySyncJobData $data)
	{
		$job = $this->updateJob($job, "Start synchronization of Widevine repository", KalturaBatchJobStatus::QUEUED);
				
		switch ($data->syncMode)
		{
			case KalturaWidevineRepositorySyncMode::MODIFY:
				$this->sendModifyRequest($job, $data);
				break;
			default:
				throw new kApplicativeException(null, "Unknown sync mode [".$data->syncMode. "]");
		}

		if($data->monitorSyncCompletion)
			return $this->closeJob($job, null, null, "Sync request sent successfully", KalturaBatchJobStatus::ALMOST_DONE, $data); 
		else 
			return $this->closeJob($job, null, null, "Sync request sent successfully", KalturaBatchJobStatus::FINISHED, $data);
	}		

	/**
	 * Send asset notify request to VOD Dealer to update widevine assets
	 * 
	 * @param KalturaBatchJob $job
	 * @param KalturaWidevineRepositorySyncJobData $data
	 */
	private function sendModifyRequest(KalturaBatchJob $job, KalturaWidevineRepositorySyncJobData $data)
	{
		KalturaLog::debug("Starting sendModifyRequest");
		
		$cgiUrl = self::$taskConfig->params->vodPackagerHost . WidevinePlugin::ASSET_NOTIFY_CGI;
		
		$dataWrap = new WidevineRepositorySyncJobDataWrap($data);		
		$widevineAssets = $dataWrap->getWidevineAssetIds();
		$licenseStartDate = $dataWrap->getLicenseStartDate();
		$licenseEndDate = $dataWrap->getLicenseEndDate();

		foreach ($widevineAssets as $assetId) 
		{
			$this->updateWidevineAsset($assetId, $licenseStartDate, $licenseEndDate, $cgiUrl);
		}
	}
	
	private function prepareAssetNotifyGetRequestXml($assetId)
	{		
		$requestInput = new WidevineAssetNotifyRequest(WidevineAssetNotifyRequest::REQUEST_GET, self::$taskConfig->params->portal);
		
		$requestInput->setAssetId($assetId);
		$requestXml = $requestInput->createAssetNotifyRequestXml();
			
		KalturaLog::debug('Asset notify request: '.$requestXml);	
													  
		return $requestXml;
	}
	
	private function prepareAssetNotifyRegisterRequestXml(WidevineAssetNotifyResponse $assetGetResponse, $licenseStartDate, $licenseEndDate)
	{
		$requestInput = new WidevineAssetNotifyRequest(WidevineAssetNotifyRequest::REQUEST_REGISTER, self::$taskConfig->params->portal);
		$requestInput->setAssetName($assetGetResponse->getName());
		$requestInput->setPolicy($assetGetResponse->getPolicy());
		$requestInput->setLicenseStartDate($licenseStartDate);
		$requestInput->setLicenseEndDate($licenseEndDate);
		$requestXml = $requestInput->createAssetNotifyRequestXml();
			
		KalturaLog::debug('Asset notify request: '.$requestXml);	
													  
		return $requestXml;
	}
	
	/**
	 * 1. Send get request to retrive widevine asset details
	 * 2. Send register requester with override option to update widevine asset
	 * 
	 * @param int $assetId
	 * @param string $licenseStartDate
	 * @param string $licenseEndDate
	 * @param string $cgiUrl
	 * @throws kApplicativeException
	 */
	private function updateWidevineAsset($assetId, $licenseStartDate, $licenseEndDate, $cgiUrl)
	{
		KalturaLog::debug("Update asset [".$assetId."] license start date [".$licenseStartDate.'] license end date ['.$licenseEndDate.']');
		
		$getAssetXml = $this->prepareAssetNotifyGetRequestXml($assetId);
		$assetGetResponseXml = WidevineAssetNotifyRequest::sendPostRequest($cgiUrl, $getAssetXml);
		$assetGetResponse = WidevineAssetNotifyResponse::createWidevineAssetNotifyResponse($assetGetResponseXml);
		$registerAssetXml = $this->prepareAssetNotifyRegisterRequestXml($assetGetResponse, $licenseStartDate, $licenseEndDate);
			
		$assetRegisterResponseXml = WidevineAssetNotifyRequest::sendPostRequest($cgiUrl, $registerAssetXml);
		$assetRegisterResponse = WidevineAssetNotifyResponse::createWidevineAssetNotifyResponse($assetRegisterResponseXml);
		if(!$assetRegisterResponse->isSuccess())
			throw new kApplicativeException($assetRegisterResponse->getStatus(), "Failed to re-register asset: ".$assetRegisterResponse->getStatusText());
	}
}
