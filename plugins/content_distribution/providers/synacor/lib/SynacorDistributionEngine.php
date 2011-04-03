<?php
/**
 * @package plugins.synacorDistribution
 * @subpackage lib
 */
class SynacorDistributionEngine extends DistributionEngine implements 
	IDistributionEngineSubmit,
	IDistributionEngineCloseSubmit, 
	IDistributionEngineUpdate,
	IDistributionEngineDelete,
	IDistributionEngineReport
{
	protected $tempXmlPath;

	private $domain = 'pubftp.synacor.com';
	private $submitPath = '/';
	private $updatePath = '/';
	private $deletePath = '/';
	
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
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaSynacorDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaSynacorDistributionProfile");
	
		return $this->doSubmit($data, $data->distributionProfile);
	}

	protected function newCustomDataElement($title, $value = '')
	{
		$customDataElement = new SynacorCustomDataElement();
		$customDataElement->title = $title;
		$customDataElement->value = $value;
		return $customDataElement;
	}
	
	private function getFlavorFormat($containerFormat)
	{
		$containerFormat = trim(strtolower($containerFormat));
		if(isset(self::$containerFormatMap[$containerFormat]))
			return self::$containerFormatMap[$containerFormat];
			
		return SynacorFormat::_UNKNOWN;
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaSynacorDistributionProfile $distributionProfile
	 * @return array()
	 */
	public function getSynacorProps(KalturaBaseEntry $entry, KalturaDistributionJobData $data, KalturaSynacorDistributionProfile $distributionProfile)
	{	
		$metadataObjects = $this->getMetadataObjects($data->entryDistribution->partnerId, $data->entryDistribution->entryId);
		$props = array();
		$props['tags'] = explode(",",$this->findMetadataValue($metadataObjects, 'Keywords'));
		$props['title'] = $this->findMetadataValue($metadataObjects, 'LongTitle');
		$props['channel'] = $this->findMetadataValue($metadataObjects, 'SynacorCategory');
		$props['description'] = $this->findMetadataValue($metadataObjects, 'ShortDescription');
		$props['date'] = time();
		$props['language'] = 'en';
		$props['published']= true;
		

		return $props;
	}
	
	public function doSubmit(KalturaDistributionSubmitJobData $data, KalturaSynacorDistributionProfile $distributionProfile)
	{
		$username = $distributionProfile->user;
		$password = $distributionProfile->password;

		$videoFilePath = $data->providerData->videoAssetFilePath;
		
		if (!$videoFilePath)
			throw new KalturaException('No video asset to distribute, the job will fail');
			
		if (!file_exists($videoFilePath))
			throw new KalturaDistributionException('The file ['.$videoFilePath.'] was not found (probably not synced yet), the job will retry');
		
		$fileTransferMgr = kFileTransferMgr::getInstance(kFileTransferMgrType::SFTP);
		if(!$fileTransferMgr)
			throw new Exception("SFTP manager not loaded");

		$destFile = "/incoming/{$data->entryDistribution->entryId}.flv";
			
		KalturaLog::debug("Syncaor: about to upload [$destFile] from [$videoFilePath]");
		$fileTransferMgr->login($this->domain, $username, $password);
		
		$fileTransferMgr->putFile($destFile, $videoFilePath, true);

		return true;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		return;
		$distributionProfile = $data->distributionProfile;
		$dailyMotionImpl = new DailyMotionImpl($distributionProfile->user, $distributionProfile->password);
		
		$status = $dailyMotionImpl->getStatus($data->remoteId);
				
		switch($status)
		{
			case 'encoding_error':
				throw new Exception("Synacor error encoding");
							
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
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaSynacorDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaSynacorDistributionProfile");
	
		return $this->doUpdate($data, $data->distributionProfile);
	}
	
	public function doUpdate(KalturaDistributionUpdateJobData $data, KalturaSynacorDistributionProfile $distributionProfile)
	{
		return;
		$entry = $this->getEntry($data->entryDistribution->partnerId, $data->entryDistribution->entryId);
		$props = $this->getSynacorProps($entry, $data, $distributionProfile);
	
		$dailyMotionImpl = new DailyMotionImpl($distributionProfile->user, $distributionProfile->password);
		$dailyMotionImpl->update($data->remoteId, $props);
		
//		$data->sentData = $synacorMediaService->request;
//		$data->results = $synacorMediaService->response;
		
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineDelete::delete()
	 */
	public function delete(KalturaDistributionDeleteJobData $data)
	{
		return;
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