<?php
/**
 * @package plugins.metroPcsDistribution
 * @subpackage lib
 */
class MetroPcsDistributionEngine extends DistributionEngine implements 
	IDistributionEngineSubmit,
	IDistributionEngineCloseSubmit,
	IDistributionEngineUpdate,
	IDistributionEngineCloseUpdate,
	IDistributionEngineDelete,
	IDistributionEngineCloseDelete
{
	const FEED_TEMPLATE = 'feed_template.xml';
	
	const METRO_PCS_STATUS_PUBLISHED = 'PUBLISHED';
	const METRO_PCS_STATUS_PENDING = 'PENDING';
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		$this->validateJobDataObjectTypes($data);
		
		$this->handleSubmit($data, $data->distributionProfile, $data->providerData);
		
		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		$this->validateJobDataObjectTypes($data);
		
		// metro pcs didn't approve that this logic does work, for now just mark every submited xml as successful
		return true;
		/*
		$publishState = $this->fetchStatus($data);
		switch($publishState)
		{
			case self::METRO_PCS_STATUS_PUBLISHED:
				return true;
			case self::METRO_PCS_STATUS_PENDING:
				return false;
			default:
				throw new Exception("Error [$publishState]");
		}
		*/
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IDistributionEngineUpdate::update()
	 */
	public function update(KalturaDistributionUpdateJobData $data)
	{
		$this->validateJobDataObjectTypes($data);
		
		$this->handleSubmit($data, $data->distributionProfile, $data->providerData);
		
		return true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IDistributionEngineCloseUpdate::closeUpdate()
	 */
	public function closeUpdate(KalturaDistributionUpdateJobData $data)
	{
		$this->validateJobDataObjectTypes($data);
		return true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IDistributionEngineDelete::delete()
	 */
	public function delete(KalturaDistributionDeleteJobData $data)
	{
		$this->validateJobDataObjectTypes($data);
		
		$this->handleDelete($data, $data->distributionProfile, $data->providerData);
		
		return true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IDistributionEngineCloseDelete::closeDelete()
	 */
	public function closeDelete(KalturaDistributionDeleteJobData $data)
	{
		$this->validateJobDataObjectTypes($data);
		return true;
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @throws Exception
	 */
	protected function validateJobDataObjectTypes(KalturaDistributionJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaMetroPcsDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaMetroPcsDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaMetroPcsDistributionJobProviderData))
			throw new Exception("Provider data must be of type KalturaMetroPcsDistributionJobProviderData");
	}
	
	/**
	 * @param string $path
	 * @param KalturaDistributionJobData $data
	 * @param KalturaMetroPcsDistributionProfile $distributionProfile
	 * @param KalturaMetroPcsDistributionJobProviderData $providerData
	 */
	public function handleSubmit(KalturaDistributionJobData $data, KalturaMetroPcsDistributionProfile $distributionProfile, KalturaMetroPcsDistributionJobProviderData $providerData)
	{
		$entryDistribution = $data->entryDistribution;
			
		//getting first flavor
		$flavorAssetsLocalPaths = unserialize($providerData->assetLocalPaths);
		//getting thumbnail urls
		$thumbUrls = unserialize($providerData->thumbUrls);
		reset($flavorAssetsLocalPaths);
		$firstFlavorAssetId = key($flavorAssetsLocalPaths);
		$firstFlavorAssetPath = $flavorAssetsLocalPaths[$firstFlavorAssetId];
		$flavorAssetArray = $this->getFlavorAsset($entryDistribution, $firstFlavorAssetId);	
		$flavorAsset = 	$flavorAssetArray[0];
		//getting thumbnails
		$thumbAssets = $this->getThumbAssets($entryDistribution);		
		$entryDuration = $this->getEntryDuration($entryDistribution);
		
		//building feed
		$currentTime = date('Y-m-d_H-i-s');
		$feed = new MetroPcsDistributionFeedHelper(self::FEED_TEMPLATE, $entryDistribution, $distributionProfile, $providerData);	
		$feed->setFlavor($flavorAsset, $entryDuration, $currentTime);
		$feed->setThumbnails($thumbAssets, $thumbUrls);
		
		//xml file
		$xmlFileName = $currentTime. '_' .$entryDistribution->id. '_' .$data->entryDistribution->entryId .'.xml';
		$path = $distributionProfile->ftpPath;
		$destXmlFile = "{$path}/{$xmlFileName}";		
		$xmlString = $feed->getXmlString();
		
		KalturaLog::info('result xml - '.PHP_EOL.$xmlString);
		$tempFile = kFile::createTempFile($xmlString, 'tmp');
		
		//load the FTP
		$ftpManager = $this->getFTPManager($distributionProfile);
		if(!$ftpManager)
			throw new Exception("FTP manager not loaded");		
			
		//upload flavor file to FTP	
		$this->uploadFlavorAssetFile($path, $feed, $providerData, $ftpManager, $flavorAsset, $currentTime);
			
		//upload feed xml file to FTP
		$ftpManager->putFile($destXmlFile, $tempFile, true);			
		
		$data->remoteId = $xmlFileName;
		$data->sentData = $xmlString;
	}	
	
	/**
	 * @param string $path
	 * @param KalturaDistributionJobData $data
	 * @param KalturaMetroPcsDistributionProfile $distributionProfile
	 * @param KalturaMetroPcsDistributionJobProviderData $providerData
	 */
	public function handleDelete(KalturaDistributionJobData $data, KalturaMetroPcsDistributionProfile $distributionProfile, KalturaMetroPcsDistributionJobProviderData $providerData)
	{
		$entryDistribution = $data->entryDistribution;
		$entryDuration = $this->getEntryDuration($entryDistribution);
		
		//building feed
		$currentTime = date('Y-m-d_H-i-s');
		$feed = new MetroPcsDistributionFeedHelper(self::FEED_TEMPLATE, $entryDistribution, $distributionProfile, $providerData);	
		//set end time and start time
		$feed->setTimesForDelete();
		//ignoring the image and item tags
		$feed->setImageIgnore();
		$feed->setItemIgnore();
		
		//xml file
		$xmlFileName = $currentTime. '_' .$entryDistribution->id. '_' .$data->entryDistribution->entryId .'.xml';		
		$path = $distributionProfile->ftpPath;
		$destXmlFile = "{$path}/{$xmlFileName}";		
		$xmlString = $feed->getXmlString();	
		KalturaLog::info('result xml - '.PHP_EOL.$xmlString);
		$tempFile = kFile::createTempFile($xmlString, 'tmp');
	
		//load the FTP
		$ftpManager = $this->getFTPManager($distributionProfile);
		if(!$ftpManager)
			throw new Exception("FTP manager not loaded");		
			
		//upload feed xml file to FTP
		$ftpManager->putFile($destXmlFile, $tempFile, true);			
		
		$data->remoteId = $xmlFileName;
		$data->sentData = $xmlString;
	}
	
	/**
	 * 
	 * @param KalturaMetroPcsDistributionProfile $distributionProfile
	 * @return ftpMgr
	 */
	protected function getFTPManager(KalturaMetroPcsDistributionProfile $distributionProfile)
	{
		$host = $distributionProfile->ftpHost;
		$login = $distributionProfile->ftpLogin;
		$pass = $distributionProfile->ftpPass;
		$engineOptions = isset(KBatchBase::$taskConfig->engineOptions) ? KBatchBase::$taskConfig->engineOptions->toArray() : array();
		$ftpManager = kFileTransferMgr::getInstance(kFileTransferMgrType::FTP, $engineOptions);
		$ftpManager->login($host, $login, $pass);
		return $ftpManager;
	}
	
	/**
	 * @param KalturaDistributionSubmitJobData $data
	 * @return string status
	 */
	protected function fetchStatus(KalturaDistributionJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaMetroPcsDistributionProfile))
			return KalturaLog::err("Distribution profile must be of type KalturaMetroPcsDistributionProfile");
	
		$fileArray = $this->fetchFilesList($data->distributionProfile);
		
		for	($i=0; $i<count($fileArray); $i++)
		{
			if (preg_match ( "/{$data->remoteId}.rcvd/" , $fileArray[$i] , $matches))
			{
				return self::METRO_PCS_STATUS_PUBLISHED;
			}
			else if (preg_match ( "/{$data->remoteId}.*.err/" , $fileArray[$i] , $matches))
			{
				//$res = preg_split ("/\./", $matches[0]);
				//return $res[1];
				$res = explode('.',$matches[0]);
				return $res[1];			
			}
		}

		return self::METRO_PCS_STATUS_PENDING;
	}

	/**
	 * @param KalturaMetroPcsDistributionProfile $distributionProfile
	 */
	protected function fetchFilesList(KalturaMetroPcsDistributionProfile $distributionProfile)
	{
		$host = $distributionProfile->ftpHost;
		$login = $distributionProfile->ftpLogin;
		$pass = $distributionProfile->ftpPass;
		
		$engineOptions = isset(KBatchBase::$taskConfig->engineOptions) ? KBatchBase::$taskConfig->engineOptions->toArray() : array();
		$fileTransferMgr = kFileTransferMgr::getInstance(kFileTransferMgrType::FTP, $engineOptions);
		if(!$fileTransferMgr)
			throw new Exception("FTP manager not loaded");
			
		$fileTransferMgr->login($host, $host, $pass);
		return $fileTransferMgr->listDir('/');
	}
	
	protected function getThumbAssets(KalturaEntryDistribution $entryDistribution)
	{
		$thumbAssetFilter = new KalturaThumbAssetFilter();
		$thumbAssetFilter->entryIdEqual = $entryDistribution->entryId;
		$thumbAssetFilter->idIn = $entryDistribution->thumbAssetIds;
		
		try {
			KBatchBase::impersonate($entryDistribution->partnerId);
			$thumbAssets = KBatchBase::$kClient->thumbAsset->listAction($thumbAssetFilter);
			KBatchBase::unimpersonate();
		}
		catch (Exception $e) {
			KBatchBase::unimpersonate();
			throw $e;
		}		
		return $thumbAssets->objects;		
	}
	
	protected function getFlavorAsset(KalturaEntryDistribution $entryDistribution, $flavorAssetId)
	{
		$flavorAssetFilter = new KalturaFlavorAssetFilter();
		$flavorAssetFilter->entryIdEqual = $entryDistribution->entryId;
		$flavorAssetFilter->idIn = $flavorAssetId;
		
		try {
			KBatchBase::impersonate($entryDistribution->partnerId);
			$flavorAssets = KBatchBase::$kClient->flavorAsset->listAction($flavorAssetFilter);
			KBatchBase::unimpersonate();
		}
		catch (Exception $e) {
			KBatchBase::unimpersonate();
			throw $e;
		}		
		return $flavorAssets->objects;		
	}
	
	protected function getEntryDuration(KalturaEntryDistribution $entryDistribution)
	{		
		try {
			KBatchBase::impersonate($entryDistribution->partnerId);
			$entry = KBatchBase::$kClient->baseEntry->get($entryDistribution->entryId);
			KBatchBase::unimpersonate();
		}
		catch (Exception $e) {
			KBatchBase::unimpersonate();
			throw $e;
		}
		
		return $entry->duration;
	}
	
	protected function uploadFlavorAssetFile($path, $feed, $providerData, $ftpManager, $flavorAsset, $currentTime)
	{
		$destName = $feed->flavorAssetUniqueName($flavorAsset, $currentTime);
		//adding the ftp path to the dest name 
		$destName = $path.'/'.$destName;
		
		$videoAssetFilePathArray = unserialize($providerData->assetLocalPaths);
		$sourceName = $videoAssetFilePathArray[$flavorAsset->id];
		$ftpManager->putFile($destName, $sourceName, true);
	}
	
}