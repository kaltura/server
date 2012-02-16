<?php
/**
 * @package plugins.attUverseDistribution
 * @subpackage lib
 */
class AttUverseDistributionEngine extends DistributionEngine implements 
	IDistributionEngineUpdate,
	IDistributionEngineSubmit	
{
	
	const FEED_TEMPLATE = 'feed_template.xml';
	
	/* (non-PHPdoc)
	 * @see DistributionEngine::configure()
	 */
	public function configure(KSchedularTaskConfig $taskConfig)
	{
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaAttUverseDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaAttUverseDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaAttUverseDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaAttUverseDistributionJobProviderData");
		
		$this->handleSubmit($data, $data->distributionProfile, $data->providerData);
		
		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineUpdate::update()
	 */
	public function update(KalturaDistributionUpdateJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaAttUverseDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaAttUverseDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaUverseDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaUverseDistributionJobProviderData");
		
		$this->handleSubmit($data, $data->distributionProfile, $data->providerData);
		
		return true;
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaAttUverseDistributionProfile $distributionProfile
	 * @param KalturaAttUverseDistributionJobProviderData $providerData
	 */
	protected function handleSubmit(KalturaDistributionJobData $data, KalturaAttUverseDistributionProfile $distributionProfile, KalturaAttUverseDistributionJobProviderData $providerData)
	{
		KalturaLog::debug("AttUverse: submit");
		/* @var $entryDistribution EntryDistribution */
		$entryDistribution = $data->entryDistribution;		

		$remoteId = $entryDistribution->entryId;
		$data->remoteId = $remoteId;
							
		$ftpManager = $this->getFTPManager($distributionProfile);
		if(!$ftpManager)
			throw new Exception("FTP manager not loaded");		
			
		//upload video to FTP
		$remoteAssetFileUrls = array();
		$assetLocalPathsArray = unserialize($providerData->assetLocalPaths);
		if ($assetLocalPathsArray)
		{
			foreach ($assetLocalPathsArray as $assetId => $assetLocalPath)
			{
				$videoDestFilePath = $this->getRemoteFilePath($assetLocalPath, $distributionProfile->ftpPath);			
				$this->uploadAssetsFiles($ftpManager, $videoDestFilePath, $assetLocalPath);			
				$remoteAssetFileUrls[$assetId] = 'ftp://'.$distributionProfile->ftpHost.'/'.$videoDestFilePath;			
			}
		}
		//save flavor assets on provider data to use in the service				
		$providerData->remoteAssetFileUrls = serialize($remoteAssetFileUrls);
						
		//upload thumbnail to FTP
		$remoteThumbnailFileUrls = array();
		$thumbLocalPathsArray = unserialize($providerData->thumbLocalPaths);
		if ($thumbLocalPathsArray)
		{
			foreach ($thumbLocalPathsArray as $assetId => $thumbLocalPath)
			{
				$thumbnailDestFilePath = $this->getRemoteFilePath($thumbLocalPath, $distributionProfile->ftpPath);
				$this->uploadAssetsFiles($ftpManager, $thumbnailDestFilePath, $thumbLocalPath);				
				$remoteThumbnailFileUrls[$assetId] = 'ftp://'.$distributionProfile->ftpHost.'/'.$thumbnailDestFilePath;			
			}
		}
		//save thumnail assets on provider data to use in the service
		$providerData->remoteThumbnailFileUrls = serialize($remoteThumbnailFileUrls);

	}	
	
	/**
	 * 
	 * @param KalturaAttUverseDistributionProfile $distributionProfile
	 * @return ftpMgr
	 */
	protected function getFTPManager(KalturaAttUverseDistributionProfile $distributionProfile)
	{
		$host = $distributionProfile->ftpHost;
		$login = $distributionProfile->ftpUsername;
		$password = $distributionProfile->ftpPassword;
		$ftpManager = kFileTransferMgr::getInstance(kFileTransferMgrType::FTP);
		$ftpManager->login($host, $login, $password);
		return $ftpManager;
	}
	
	/**
	 * @param string $providerDataPath
	 * @return string
	 */
	protected function getRemoteFilePath($localFilePath, $ftpBasePath)
	{
		$remoteFilePath = pathinfo($localFilePath, PATHINFO_BASENAME);
		if ($ftpBasePath)
		{
			$remoteFilePath = $ftpBasePath.'/'.$remoteFilePath;
		}
		return $remoteFilePath;
	}
	
	
	protected function uploadAssetsFiles($ftpManager, $destFileName, $filePath)
	{									
		if ($ftpManager->fileExists($destFileName))
		{
			KalturaLog::err('The file ['.$destFileName.'] already exists at the FTP');
		}
		else	
		{					
			$ftpManager->putFile($destFileName, $filePath, true);
		}
	}
	

}