<?php
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
	 * @return array()
	 */
	public function getDailymotionProps(KalturaBaseEntry $entry, KalturaDistributionJobData $data, KalturaDailymotionDistributionProfile $distributionProfile, $enabled = null)
	{	
		$metadataObjects = $this->getMetadataObjects($data->entryDistribution->partnerId, $data->entryDistribution->entryId, KalturaMetadataObjectType::ENTRY, $distributionProfile->metadataProfileId);
	
		$description = $entry->description;
		$metadataDescription = $this->findMetadataValue($metadataObjects, 'DailymotionDescription');
		if($metadataDescription && strlen($metadataDescription))
			$description = $metadataDescription;
	
		$tags = $entry->tags;
		$metadataTags = $this->findMetadataValue($metadataObjects, 'DailymotionKeywords');
		if($metadataTags && strlen($metadataTags))
			$tags = $metadataTags;
		
		$props = array();
		$props['tags'] = str_replace(',', ' ', $tags);
		$props['title'] = $entry->name;
		$props['channel'] = $this->findMetadataValue($metadataObjects, 'DailymotionCategory');
		$props['description'] = $description;
		$props['date'] = time();
		$props['language'] = 'en';
		$props['published']= true;
		
		if(!is_null($enabled))
			$props['private']= !$enabled;

		return $props;
	}
	
	public function doSubmit(KalturaDistributionSubmitJobData $data, KalturaDailymotionDistributionProfile $distributionProfile)
	{	
		$enabled = false;
		if($data->entryDistribution->sunStatus == KalturaEntryDistributionSunStatus::AFTER_SUNRISE)
			$enabled = true;
		
		$needDel = false;
		$entry = $this->getEntry($data->entryDistribution->partnerId, $data->entryDistribution->entryId);
		$props = $this->getDailymotionProps($entry, $data, $distributionProfile, $enabled);
		if($data->entryDistribution->remoteId)
		{
			$dailyMotionImpl = new DailyMotionImpl($distributionProfile->user, $distributionProfile->password);
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
/*			try
			{
    		KalturaLog::debug("DM : before " . $videoFilePathNew);
    			@symlink ($videoFilePath, $videoFilePathNew);
    		KalturaLog::debug("DM : after");
    		}
    		catch(Exception $ex)
    		{
    		KalturaLog::debug("DM : exception");
    		}*/
			if (!file_exists($videoFilePathNew))
			{
				copy($videoFilePath,$videoFilePathNew);
				$needDel = true;
			}
			$videoFilePath = $videoFilePathNew;
		}
		
		$dailyMotionImpl = new DailyMotionImpl($distributionProfile->user, $distributionProfile->password);
		$remoteId = $dailyMotionImpl->upload($videoFilePath);
		$dailyMotionImpl->update($remoteId, $props);
	
		if ($needDel == true)
		{
			unlink($videoFilePath);
		}
		$data->remoteId = $remoteId;
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		$distributionProfile = $data->distributionProfile;
		$dailyMotionImpl = new DailyMotionImpl($distributionProfile->user, $distributionProfile->password);
		
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
		$entry = $this->getEntry($data->entryDistribution->partnerId, $data->entryDistribution->entryId);
		$props = $this->getDailymotionProps($entry, $data, $distributionProfile, $enabled);
	
		$dailyMotionImpl = new DailyMotionImpl($distributionProfile->user, $distributionProfile->password);
		$dailyMotionImpl->update($data->remoteId, $props);
		
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
}