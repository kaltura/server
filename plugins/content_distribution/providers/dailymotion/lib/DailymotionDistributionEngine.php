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
	IDistributionEngineReport
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

	protected function newCustomDataElement($title, $value = '')
	{
		$customDataElement = new DailymotionCustomDataElement();
		$customDataElement->title = $title;
		$customDataElement->value = $value;
		return $customDataElement;
	}
	
	private function getFlavorFormat($containerFormat)
	{
		$containerFormat = trim(strtolower($containerFormat));
		if(isset(self::$containerFormatMap[$containerFormat]))
			return self::$containerFormatMap[$containerFormat];
			
		return DailymotionFormat::_UNKNOWN;
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaDailymotionDistributionProfile $distributionProfile
	 * @return array()
	 */
	public function getDailymotionProps(/*KalturaBaseEntry $entry, */KalturaDistributionJobData $data, KalturaDailymotionDistributionProfile $distributionProfile)
	{	
		$metadataObjects = $this->getMetadataObjects($data->entryDistribution->partnerId, $data->entryDistribution->entryId);
		$props = array();
//		$props['tags'] = explode(",",$this->findMetadataValue($metadataObjects, 'Keywords'));
		$props['tags'] =''; /* temporary patch */
		$props['title'] = $this->findMetadataValue($metadataObjects, 'LongTitle');
		$props['channel'] = $this->findMetadataValue($metadataObjects, 'DailymotionCategory');
		$props['description'] = $this->findMetadataValue($metadataObjects, 'ShortDescription');
		$props['date'] = time();
		$props['language'] = 'en';
		$props['published']= true;
		

		return $props;
	}
	
	public function doSubmit(KalturaDistributionSubmitJobData $data, KalturaDailymotionDistributionProfile $distributionProfile)
	{	
		$needDel = false;
//		$entry = $this->getEntry($data->entryDistribution->partnerId, $data->entryDistribution->entryId);
		$props = $this->getDailymotionProps(/*$entry,*/ $data, $distributionProfile);
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
	
	public function doUpdate(KalturaDistributionUpdateJobData $data, KalturaDailymotionDistributionProfile $distributionProfile)
	{
//		$entry = $this->getEntry($data->entryDistribution->partnerId, $data->entryDistribution->entryId);
		$props = $this->getDailymotionProps(/*$entry, */$data, $distributionProfile);
	
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