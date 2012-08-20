<?php
/**
 * @package plugins.youtubeApiDistribution
 * @subpackage lib
 */
class YoutubeApiDistributionEngine extends DistributionEngine implements 
	IDistributionEngineSubmit,
	IDistributionEngineCloseSubmit, 
	IDistributionEngineUpdate,
	IDistributionEngineDelete,
	IDistributionEngineReport,
	IDistributionEngineEnable,
	IDistributionEngineDisable
{
	protected $tempXmlPath;
	
	protected $timeout = 10;
	
	/* (non-PHPdoc)
	 * @see DistributionEngine::configure()
	 */
	public function configure(KSchedularTaskConfig $taskConfig)
	{
		if($taskConfig->params->tempXmlPath)
		{
			$this->tempXmlPath = $taskConfig->params->tempXmlPath;
			if(!is_dir($this->tempXmlPath))
				mkdir($this->tempXmlPath, 0777, true);
		}
		else
		{
			KalturaLog::err("params.tempXmlPath configuration not supplied");
			$this->tempXmlPath = sys_get_temp_dir();
		}
		
		if (isset($taskConfig->params->youtubeApi))
		{
			if (isset($taskConfig->params->youtubeApi->timeout))
				$this->timeout = $taskConfig->params->youtubeApi->timeout;
		}
		
		KalturaLog::info('Request timeout was set to ' . $this->timeout . ' seconds');
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaYoutubeApiDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaYoutubeApiDistributionProfile");
	
		return $this->doSubmit($data, $data->distributionProfile);
	}

	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaYoutubeApiDistributionProfile $distributionProfile
	 * @return array()
	 */
	protected function getYoutubeApiProps()
	{			
		$props = array();
		$props['keywords'] = $this->getValueForField(KalturaYouTubeApiDistributionField::MEDIA_KEYWORDS);
		$props['title'] = $this->getValueForField(KalturaYouTubeApiDistributionField::MEDIA_TITLE);
		$props['category'] = $this->translateCategory($this->getValueForField(KalturaYouTubeApiDistributionField::MEDIA_CATEGORY));
		$props['description'] = $this->getValueForField(KalturaYouTubeApiDistributionField::MEDIA_DESCRIPTION);
		
		$props['start_date'] = $this->getValueForField(KalturaYouTubeApiDistributionField::START_DATE);
		$props['end_date'] = $this->getValueForField(KalturaYouTubeApiDistributionField::END_DATE);
		
		$props['comment'] = $this->getValueForField(KalturaYouTubeApiDistributionField::ALLOW_COMMENTS);
		$props['rate'] = $this->getValueForField(KalturaYouTubeApiDistributionField::ALLOW_RATINGS);
		$props['commentVote'] = $this->getValueForField(KalturaYouTubeApiDistributionField::ALLOW_RATINGS);
		$props['videoRespond'] = $this->getValueForField(KalturaYouTubeApiDistributionField::ALLOW_RESPONSES);
		$props['embed'] = $this->getValueForField(KalturaYouTubeApiDistributionField::ALLOW_EMBEDDING);		
		
		KalturaLog::debug("Props [" . print_r($props, true) . "]");

		return $props;
	}
	
	/**
	 * Tries to transalte the friendly name of the category to the api value, if not found the input value will be returned (as a fallback)
	 * @param string $category
	 */
	protected function translateCategory($category)
	{
		foreach(YouTubeApiImpl::getCategoriesMap() as $id => $name)
		{
			if ($name == $category)
				return $id;
		}
		return $category;
	}
	
	public function doSubmit(KalturaDistributionSubmitJobData $data, KalturaYoutubeApiDistributionProfile $distributionProfile)
	{	
	    $this->fieldValues = unserialize($data->providerData->fieldValues);
	    
		$private = true;
		if($data->entryDistribution->sunStatus == KalturaEntryDistributionSunStatus::AFTER_SUNRISE)
			$private = false;
		
		$needDel = false;
		$entry = $this->getEntry($data->entryDistribution->partnerId, $data->entryDistribution->entryId);
		$props = $this->getYoutubeApiProps();
		if($data->entryDistribution->remoteId)
		{
			$youTubeApiImpl = new YouTubeApiImpl($distributionProfile->username, $distributionProfile->password, $this->getHttpClientConfig());
			$youTubeApiImpl->updateEntry($data->entryDistribution->remoteId, $props, $private);
		
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
		
		$youTubeApiImpl = new YouTubeApiImpl($distributionProfile->username, $distributionProfile->password, $this->getHttpClientConfig());
		$remoteId = $youTubeApiImpl->uploadVideo($videoFilePath, $videoFilePath, $props, $private);
	
		if ($needDel == true)
		{
			unlink($videoFilePath);
		}
		$data->remoteId = $remoteId;
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		$distributionProfile = $data->distributionProfile;
		$youTubeApiImpl = new YouTubeApiImpl($distributionProfile->username, $distributionProfile->password, $this->getHttpClientConfig());
		
		$status = $youTubeApiImpl->getStatus($data->remoteId);
				
		switch($status)
		{
			case 'encoding_error':
				throw new Exception("YoutubeApi error encoding");
							
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
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaYoutubeApiDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaYoutubeApiDistributionProfile");
	
		return $this->doUpdate($data, $data->distributionProfile);
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineDisable::disable()
	 */
	public function disable(KalturaDistributionDisableJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaYoutubeApiDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaYoutubeApiDistributionProfile");
	
		return $this->doUpdate($data, $data->distributionProfile, false);
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineEnable::enable()
	 */
	public function enable(KalturaDistributionEnableJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaYoutubeApiDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaYoutubeApiDistributionProfile");
	
		return $this->doUpdate($data, $data->distributionProfile, true);
	}
	
	public function doUpdate(KalturaDistributionUpdateJobData $data, KalturaYoutubeApiDistributionProfile $distributionProfile, $enabled = null)
	{
	    $this->fieldValues = unserialize($data->providerData->fieldValues);
	    	    
		$private = true;
		if($enabled === true || (is_null($enabled) && $data->entryDistribution->sunStatus == KalturaEntryDistributionSunStatus::AFTER_SUNRISE))
			$private = false;
		
		$entry = $this->getEntry($data->entryDistribution->partnerId, $data->entryDistribution->entryId);
		$props = $this->getYoutubeApiProps();
	
		$youTubeApiImpl = new YouTubeApiImpl($distributionProfile->username, $distributionProfile->password, $this->getHttpClientConfig());
		$youTubeApiImpl->updateEntry($data->remoteId, $props, $private);
		
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineDelete::delete()
	 */
	public function delete(KalturaDistributionDeleteJobData $data)
	{
		$distributionProfile = $data->distributionProfile;
		$youTubeApiImpl = new YouTubeApiImpl($distributionProfile->username, $distributionProfile->password, $this->getHttpClientConfig());
		
		$youTubeApiImpl->deleteEntry($data->remoteId);
		
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineReport::fetchReport()
	 */
	public function fetchReport(KalturaDistributionFetchReportJobData $data)
	{
		// TODO
	}
	
	/**
	 * @return array
	 */
	protected function getHttpClientConfig()
	{
		return array('timeout' => $this->timeout);
	}
	
	private function getValueForField($fieldName)
	{
	    if (isset($this->fieldValues[$fieldName])) {
	        return $this->fieldValues[$fieldName];
	    }
	    return null;
	}
}