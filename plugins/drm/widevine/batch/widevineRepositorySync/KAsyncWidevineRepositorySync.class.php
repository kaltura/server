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
		
		$dataWrap = new WidevineRepositorySyncJobDataWrap($data);		
		$widevineAssets = $dataWrap->getWidevineAssetIds();
		$licenseStartDate = $dataWrap->getLicenseStartDate();
		$licenseEndDate = $dataWrap->getLicenseEndDate();

		foreach ($widevineAssets as $assetId) 
		{
			$this->updateWidevineAsset($assetId, $licenseStartDate, $licenseEndDate);
		}
		
		$this->updateFlavorAssets($job, $dataWrap);
	}
	
	/**
	 * Execute register asset with new details to update exisiting asset
	 * 
	 * @param int $assetId
	 * @param string $licenseStartDate
	 * @param string $licenseEndDate
	 * @throws kApplicativeException
	 */
	private function updateWidevineAsset($assetId, $licenseStartDate, $licenseEndDate)
	{
		KalturaLog::debug("Update asset [".$assetId."] license start date [".$licenseStartDate.'] license end date ['.$licenseEndDate.']');
		
		$errorMessage = '';
		
		$wvAssetId = KWidevineBatchHelper::sendRegisterAssetRequest(
										self::$taskConfig->params->wvLicenseServerUrl,
										null,
										$assetId,
										self::$taskConfig->params->portal,
										null,
										$licenseStartDate,
										$licenseEndDate,
										$errorMessage);				
		
		if(!$wvAssetId)
		{
			KBatchBase::unimpersonate();
			
			$logMessage = 'Asset update failed, asset id: '.$assetId.' error: '.$errorMessage;
			KalturaLog::err($logMessage);
			throw new kApplicativeException(null, $logMessage);
		}			
	}
	
	/**
	 * Update flavorAsset in Kaltura after the distribution dates apllied to Wideivne asset
	 * 
	 * @param KalturaBatchJob $job
	 * @param WidevineRepositorySyncJobDataWrap $dataWrap
	 */
	private function updateFlavorAssets(KalturaBatchJob $job, WidevineRepositorySyncJobDataWrap $dataWrap)
	{
		$this->impersonate($job->partnerId);
		
		$startDate = $dataWrap->getLicenseStartDate();
		$endDate = $dataWrap->getLicenseEndDate();	
		
		$filter = new KalturaAssetFilter();
		$filter->entryIdEqual = $job->entryId;
		$filter->tagsLike = 'widevine';
		$flavorAssetsList = self::$kClient->flavorAsset->listAction($filter, new KalturaFilterPager());
		
		foreach ($flavorAssetsList->objects as $flavorAsset) 
		{
			if($flavorAsset instanceof KalturaWidevineFlavorAsset && $dataWrap->hasAssetId($flavorAsset->widevineAssetId))
			{
				KalturaLog::debug('Updating flavor asset ['.$flavorAsset->id.']');	
				
				$updatedFlavorAsset = new KalturaWidevineFlavorAsset();
				$updatedFlavorAsset->widevineDistributionStartDate = $startDate;
				$updatedFlavorAsset->widevineDistributionEndDate = $endDate;
				self::$kClient->flavorAsset->update($flavorAsset->id, $updatedFlavorAsset);
			}		
		}		
		$this->unimpersonate();
	}
}
