<?php
/**
 * @package plugins.dailymotionDistribution
 * @subpackage lib
 */
require_once 'DailymotionImpl.php';

/**
 * @package plugins.dailymotionDistribution
 * @subpackage lib
 */
class DailymotionDistributionEngine extends DistributionEngine implements 
	IDistributionEngineSubmit,
	IDistributionEngineCloseSubmit, 
	IDistributionEngineUpdate,
	IDistributionEngineDelete,
	IDistributionEngineReport,
	IDistributionEngineEnable,
	IDistributionEngineDisable
{
	protected $tempXmlPath;
	
	protected $requestTimeout = 10;
	
	protected $connectTimeout = 15;
	
	protected $fieldValues;
	
	/* (non-PHPdoc)
	 * @see DistributionEngine::configure()
	 */
	public function configure()
	{
		parent::configure();
		
		if(KBatchBase::$taskConfig->params->tempXmlPath)
		{
			$this->tempXmlPath = KBatchBase::$taskConfig->params->tempXmlPath;
			if(!is_dir($this->tempXmlPath))
				mkdir($this->tempXmlPath, 0777, true);
		}
		else
		{
			KalturaLog::err("params.tempXmlPath configuration not supplied");
			$this->tempXmlPath = sys_get_temp_dir();
		}
		
		if (isset(KBatchBase::$taskConfig->params->dailymotion))
		{
			if (isset(KBatchBase::$taskConfig->params->dailymotion->requestTimeout))
				$this->requestTimeout = KBatchBase::$taskConfig->params->dailymotion->requestTimeout;
				
			if (isset(KBatchBase::$taskConfig->params->dailymotion->connectTimeout))
				$this->connectTimeout = KBatchBase::$taskConfig->params->dailymotion->connectTimeout;
		}
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaDailymotionDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaDailymotionDistributionProfile");
	
		return $this->doSubmit($data, $data->distributionProfile);
	}

	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaDailymotionDistributionProfile $distributionProfile
	 * @param KalturaDailymotionDistributionJobProviderData $providerData
	 * @return array()
	 */
	public function getDailymotionProps($enabled = null, $distributionProfile = null, $providerData = null)
	{
		$props = array();
		$props['tags'] = str_replace(',', ' , ', $this->getValueForField(KalturaDailymotionDistributionField::VIDEO_TAGS));
		$props['title'] = $this->getValueForField(KalturaDailymotionDistributionField::VIDEO_TITLE);
		$props['channel'] = $this->translateCategory($this->getValueForField(KalturaDailymotionDistributionField::VIDEO_CHANNEL));
		$props['description'] = $this->getValueForField(KalturaDailymotionDistributionField::VIDEO_DESCRIPTION);
		//$props['date'] = time();
		$props['language'] = $this->getValueForField(KalturaDailymotionDistributionField::VIDEO_LANGUAGE);
		$props['type'] = $this->getValueForField(KalturaDailymotionDistributionField::VIDEO_TYPE);
		$props['published']= true;
		if(!is_null($enabled))
			$props['private']= !$enabled;

		$geoBlocking = $this->getGeoBlocking($distributionProfile, $providerData);

		KalturaLog::info('Geo blocking array: '.print_r($geoBlocking, true));
		if (count($geoBlocking))
			$props['geoblocking'] = $geoBlocking;

		return $props;
	}

	/**
	 * Tries to transalte the friendly name of the category to the api value, if not found the same value will be returned (as a fallback)
	 * @param string $category
	 */
	protected function translateCategory($category)
	{
		foreach(DailyMotionImpl::getCategoriesMap() as $id => $name)
		{
			if ($name == $category)
				return $id;
		}
		return $category;
	}
	
	
	public function doSubmit(KalturaDistributionSubmitJobData $data, KalturaDailymotionDistributionProfile $distributionProfile)
	{	
	    $this->fieldValues = unserialize($data->providerData->fieldValues);
	    
		$enabled = false;
		if($data->entryDistribution->sunStatus == KalturaEntryDistributionSunStatus::AFTER_SUNRISE)
			$enabled = true;
		
		$needDel = false;

		$props = $this->getDailymotionProps($enabled, $distributionProfile, $data->providerData);

		if($data->entryDistribution->remoteId)
		{
			$dailyMotionImpl = new DailyMotionImpl($distributionProfile->user, $distributionProfile->password);
			$this->configureTimeouts($dailyMotionImpl);
			$dailyMotionImpl->update($data->remoteId, $props);
		
			$data->remoteId = $data->entryDistribution->remoteId;
			return true;
		}
			
		$videoFilePath = $data->providerData->videoAssetFilePath;
		
		if (!$videoFilePath)
			throw new KalturaException('No video asset to distribute, the job will fail');
			
		if (!file_exists($videoFilePath))
			throw new KalturaDistributionException('The file ['.$videoFilePath.'] was not found (probably not synced yet), the job will retry');
		
		if (FALSE === strstr($videoFilePath, "."))
		{
			$videoFilePathNew = $this->tempXmlPath . "/" . uniqid() . ".dme";
			if (!file_exists($videoFilePathNew))
			{
				copy($videoFilePath,$videoFilePathNew);
				$needDel = true;
			}
			$videoFilePath = $videoFilePathNew;
		}
		
		$dailyMotionImpl = new DailyMotionImpl($distributionProfile->user, $distributionProfile->password);
		$this->configureTimeouts($dailyMotionImpl);
		$remoteId = $dailyMotionImpl->upload($videoFilePath);
		$dailyMotionImpl->update($remoteId, $props);
	
		if ($needDel == true)
		{
			unlink($videoFilePath);
		}
		
		$data->remoteId = $remoteId;
		$captionsInfo = $data->providerData->captionsInfo;
		/* @var $captionInfo KalturaDailymotionDistributionCaptionInfo */
		foreach ($captionsInfo as $captionInfo){
			if ($captionInfo->action == KalturaDailymotionDistributionCaptionAction::SUBMIT_ACTION){
				$data->mediaFiles[] = $this->submitCaption($dailyMotionImpl, $captionInfo, $data->remoteId);
			}
		}
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		$distributionProfile = $data->distributionProfile;
		$dailyMotionImpl = new DailyMotionImpl($distributionProfile->user, $distributionProfile->password);
		$this->configureTimeouts($dailyMotionImpl);
		
		$status = $dailyMotionImpl->getStatus($data->remoteId);
				
		switch($status)
		{
			case 'encoding_error':
				throw new Exception("Dailymotion error encoding");
							
			case 'waiting':
			case 'processing':
			case 'rejected':
				return false;
							
			case 'deleted':
			case 'ready':
			case 'published':
				return true;
		}
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineUpdate::update()
	 */
	public function update(KalturaDistributionUpdateJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaDailymotionDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaDailymotionDistributionProfile");
	
		return $this->doUpdate($data, $data->distributionProfile);
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineDisable::disable()
	 */
	public function disable(KalturaDistributionDisableJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaDailymotionDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaDailymotionDistributionProfile");
	
		return $this->doUpdate($data, $data->distributionProfile, false);
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineEnable::enable()
	 */
	public function enable(KalturaDistributionEnableJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaDailymotionDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaDailymotionDistributionProfile");
	
		return $this->doUpdate($data, $data->distributionProfile, true);
	}
	
	public function doUpdate(KalturaDistributionUpdateJobData $data, KalturaDailymotionDistributionProfile $distributionProfile, $enabled = null)
	{
	    $this->fieldValues = unserialize($data->providerData->fieldValues);
	    
		$props = $this->getDailymotionProps($enabled, $distributionProfile, $data->providerData);
	
		$dailyMotionImpl = new DailyMotionImpl($distributionProfile->user, $distributionProfile->password);
		$this->configureTimeouts($dailyMotionImpl);
		$dailyMotionImpl->update($data->remoteId, $props);
		
		$captionsInfo = $data->providerData->captionsInfo;
		/* @var $captionInfo KalturaYouTubeApiCaptionDistributionInfo */
		foreach ($captionsInfo as $captionInfo){
			switch ($captionInfo->action){
				case KalturaDailymotionDistributionCaptionAction::SUBMIT_ACTION:
					$data->mediaFiles[] = $this->submitCaption($dailyMotionImpl,$captionInfo, $data->remoteId);
					break;
				case KalturaDailymotionDistributionCaptionAction::UPDATE_ACTION:
					$tempFile = $this->getAssetFile($captionInfo->assetId, $this->tempDirectory);
					if (!file_exists($tempFile))
						throw new KalturaDistributionException('The caption file was not found (probably not synced yet), the job will retry');
					try
					{
						$dailyMotionImpl->updateSubtitle($captionInfo->remoteId, $captionInfo, $tempFile);
						$this->updateRemoteMediaFileVersion($data, $captionInfo);
					}
					catch (Exception $e)
					{
						unlink($tempFile);
						throw $e;
					}

					unlink($tempFile);
					break;
				case KalturaDailymotionDistributionCaptionAction::DELETE_ACTION:
					$dailyMotionImpl->deleteSubtitle($captionInfo->remoteId);
					break;
			}
		}
//		$data->sentData = $dailymotionMediaService->request;
//		$data->results = $dailymotionMediaService->response;
		
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineDelete::delete()
	 */
	public function delete(KalturaDistributionDeleteJobData $data)
	{
		$distributionProfile = $data->distributionProfile;
		$dailyMotionImpl = new DailyMotionImpl($distributionProfile->user, $distributionProfile->password);
		$this->configureTimeouts($dailyMotionImpl);
		
		$dailyMotionImpl->delete($data->remoteId);
		
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineReport::fetchReport()
	 */
	public function fetchReport(KalturaDistributionFetchReportJobData $data)
	{
		// TODO
	}
	
	protected function configureTimeouts(DailyMotionImpl $dailyMotionImpl)
	{
		KalturaLog::info('Setting connection timeout to ' . $this->connectTimeout . ' seconds');
		$dailyMotionImpl->setOption('connectionTimeout', $this->connectTimeout);
		KalturaLog::info('Setting request timeout to ' . $this->requestTimeout . ' seconds');
		$dailyMotionImpl->setOption('timeout', $this->requestTimeout);
	}
	
	
	private function getValueForField($fieldName)
	{
	    if (isset($this->fieldValues[$fieldName])) {
	        return $this->fieldValues[$fieldName];
	    }
	    return null;
	}

	/**
	 * @param KalturaDailymotionDistributionProfile $distributionProfile
	 * @param KalturaDailymotionDistributionJobProviderData $providerData
	 * @return array
	 */
	private function getGeoBlocking($distributionProfile = null, $providerData = null)
	{
		$geoBlocking = array();
		if (is_null($distributionProfile))
			return $geoBlocking;
		$geoBlockingOperation = null;
		$geoBlockingCountryList = null;
		if ($distributionProfile->geoBlockingMapping == KalturaDailymotionGeoBlockingMapping::METADATA) {
			$geoBlockingOperation = $this->getValueForField(KalturaDailymotionDistributionField::VIDEO_GEO_BLOCKING_OPERATION);
			$geoBlockingCountryList = $this->getValueForField(KalturaDailymotionDistributionField::VIDEO_GEO_BLOCKING_COUNTRY_LIST);
		}
		elseif ($distributionProfile->geoBlockingMapping == KalturaDailymotionGeoBlockingMapping::ACCESS_CONTROL) {
			$geoBlockingOperation = $providerData->accessControlGeoBlockingOperation;
			$geoBlockingCountryList = $providerData->accessControlGeoBlockingCountryList;
		}
		if ($geoBlockingOperation)
				$geoBlocking[] = $geoBlockingOperation;
		if ($geoBlockingCountryList)
				$geoBlocking = array_merge($geoBlocking, explode(',', $geoBlockingCountryList));

		foreach($geoBlocking as &$tmpstr)
			$tmpstr = strtolower($tmpstr);
		return $geoBlocking;
	}
	
	private function submitCaption(DailymotionImpl $dailymotionImpl, $captionInfo, $remoteId)
	{
		$tempFile = $this->getAssetFile($captionInfo->assetId, $this->tempDirectory);
		if (!$tempFile)
			throw new KalturaDistributionException('The caption file was not found (probably not synced yet), the job will retry');

		KalturaLog::info ( 'Submitting caption [' . $captionInfo->assetId . ']' );
		try
		{
			$captionRemoteId = $dailymotionImpl->uploadSubtitle($remoteId, $captionInfo, $tempFile);
		}
		catch (Exception $e)
		{
			unlink($tempFile);
			throw $e;
		}

		unlink($tempFile);
		return $this->getNewRemoteMediaFile ( $captionRemoteId, $captionInfo );
	}
	
	private function getNewRemoteMediaFile($captionRemoteId , $captionInfo) {
		$remoteMediaFile = new KalturaDistributionRemoteMediaFile ();
		$remoteMediaFile->remoteId = $captionRemoteId;
		$remoteMediaFile->version = $captionInfo->version;
		$remoteMediaFile->assetId = $captionInfo->assetId;
		return $remoteMediaFile;
	}
}