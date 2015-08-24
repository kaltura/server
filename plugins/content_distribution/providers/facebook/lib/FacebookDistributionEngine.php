<?php

/**
 * @package plugins.facebookDistribution
 * @subpackage lib
 */
class FacebookDistributionEngine extends DistributionEngine implements IDistributionEngineSubmit, IDistributionEngineCloseSubmit, IDistributionEngineUpdate
{
	protected $appId;
	protected $appSecret;
	
	/* (non-PHPdoc)
	 * @see DistributionEngine::configure()
	 */
	public function configure()
	{
		parent::configure();
		if (isset(KBatchBase::$taskConfig->params->facebook))
		{
			if (isset(KBatchBase::$taskConfig->params->facebook->appId))
				$this->appId = KBatchBase::$taskConfig->params->facebook->appId;
			if (isset(KBatchBase::$taskConfig->params->facebook->appSecret))
				$this->appSecret = KBatchBase::$taskConfig->params->facebook->appSecret;
		}
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		$this->validate($data);	
		if($data->entryDistribution->remoteId)
		{
			$data->remoteId = $data->entryDistribution->remoteId;
		}
		else 
		{
			$this->doSubmit($data, $data->distributionProfile);
		}
		return true;
	}
	
	protected function doSubmit(KalturaDistributionSubmitJobData $data, KalturaFacebookDistributionProfile $distributionProfile)
	{
		$videoPath = $data->providerData->videoAssetFilePath;
		if (!$videoPath)
			throw new KalturaException('No video asset to distribute, the job will fail');
		if (!file_exists($videoPath))
			throw new KalturaDistributionException("The file [$videoPath] was not found (probably not synced yet), the job will retry");
		
		$fieldValues = unserialize($data->providerData->fieldValues);
							
		$data->remoteId = FacebookGraphSdkUtils::uploadVideo(
													$this->appId, 
													$this->appSecret, 
													$distributionProfile->getPageId(), 
													$distributionProfile->getPageAccessToken(),
													$videoPath, 
													filesize($videoPath), 
													$this->tempDirectory,
													$fieldValues);
		//TODO - thumbnail
		foreach ($data->providerData->captionsInfo as $captionInfo)
		{
			if ($captionInfo->action == KalturaDistributionAction::SUBMIT)
			{
				$this->submitCaption($captionInfo, $data->remoteId);
			}
		}
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineUpdate::update()
	 */
	public function update(KalturaDistributionUpdateJobData $data)
	{
		$this->validate($data);
		return $this->doUpdate($data, $data->distributionProfile);
	}

	protected function doUpdate(KalturaDistributionUpdateJobData $data, KalturaFacebookDistributionProfile $distributionProfile)
	{
		$fieldValues = unserialize($data->providerData->fieldValues); //TODO

		foreach ($data->providerData->captionsInfo as $captionInfo)
		{
			switch ($captionInfo->action){
				case KalturaDistributionAction::SUBMIT:
					$data->mediaFiles[] = $this->submitCaption($distributionProfile, $captionInfo, $data->entryDistribution->remoteId);
					break;
				case KalturaDistributionAction::DELETE:
					$this->deleteCaption($distributionProfile, $captionInfo, $data->entryDistribution->remoteId);
					break;
			}
		}		
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		$this->validate($data);	
		return $this->doCloseSubmit($data, $data->distributionProfile);
	}

	protected function doCloseSubmit(KalturaDistributionSubmitJobData $data, KalturaFacebookDistributionProfile $distributionProfile)
	{	
		//TODO
	}
	
	protected function submitCaption(KalturaFacebookDistributionProfile $distributionProfile, KalturaCaptionDistributionInfo $captionInfo, $remoteId)
	{
		$status = FacebookGraphSdkUtils::uploadCaptions(
									$this->appId, 
									$this->appSecret, 
									$distributionProfile->getPageAccessToken(),
									$remoteId, 
									$captionInfo->filePath,
									$captionInfo->language,							
									$this->tempDirectory);
		return $status;			
	}
	
	protected function deleteCaption(KalturaFacebookDistributionProfile $distributionProfile, KalturaCaptionDistributionInfo $captionInfo, $remoteId)
	{
		$status = FacebookGraphSdkUtils::deleteCaptions(
									$this->appId, 
									$this->appSecret, 
									$distributionProfile->getPageAccessToken(),
									$remoteId, 
									$captionInfo->language);

		return $status;
	}	
	
	protected function validate(KalturaDistributionJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaFacebookDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaFacebookDistributionProfile");

		if(!$this->appId)
			throw new Exception("Facebook appId is not configured");
			
		if(!$this->appSecret)
			throw new Exception("Facebook appSecret is not configured");
	}
}