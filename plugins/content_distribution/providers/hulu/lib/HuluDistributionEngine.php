<?php
/**
 * @package plugins.huluDistribution
 * @subpackage lib
 */
class HuluDistributionEngine extends DistributionEngine implements 
	IDistributionEngineSubmit,
	IDistributionEngineCloseSubmit
{
	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 * 
	 * Demonstrate asynchronous external API usage
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		// validates received object types
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaHuluDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaHuluDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaHuluDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaHuluDistributionJobProviderData");
		
		// call the actual submit action
		$this->handleSubmit($data, $data->distributionProfile, $data->providerData);
		
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		$entryId = $data->entryDistribution->entryId;
		$loginName = $data->distributionProfile->sftpLogin;
		$loginPass = $data->distributionProfile->sftpPass;
		
		return true;
	}

	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaHuluDistributionProfile $distributionProfile
	 * @param KalturaHuluDistributionJobProviderData $providerData
	 */
	protected function handleSubmit(KalturaDistributionJobData $data, KalturaHuluDistributionProfile $distributionProfile, KalturaHuluDistributionJobProviderData $providerData)
	{
		$feed = new HuluFeedHelper('hulu_template.xml', $distributionProfile, $providerData);
		$xml = $feed->getXml();
		
		$sftpManager = $this->getSFTPManager($distributionProfile);
		
		$videoFilePath = $providerData->videoAssetFilePath;
		$thumbAssetFilePath = $providerData->thumbAssetFilePath;
		
		$sftpBasePath = '/home/' . $distributionProfile->sftpLogin . '/upload';
		$videoSFTPPath 	= $sftpBasePath.'/'.$providerData->fileBaseName.'.'.pathinfo($videoFilePath, PATHINFO_EXTENSION);
		$thumbSFTPPath 	= $sftpBasePath.'/'.$providerData->fileBaseName.'.'.pathinfo($thumbAssetFilePath, PATHINFO_EXTENSION);
		$xmlSFTPPath 	= $sftpBasePath.'/'.$providerData->fileBaseName.'.xml';
		KalturaLog::info('$videoSFTPPath:' . $videoSFTPPath);
		KalturaLog::info('$thumbSFTPPath:' . $thumbSFTPPath);
		KalturaLog::info('$xmlSFTPPath:' . $xmlSFTPPath);
		KalturaLog::info('XML:' . $xml);
		
		$sftpManager->putFile($videoSFTPPath, $videoFilePath);
		$sftpManager->putFile($thumbSFTPPath, $thumbAssetFilePath);
		$sftpManager->filePutContents($xmlSFTPPath, $xml);
	}
	
	/* (non-PHPdoc)
	 * @see DistributionEngine::configure()
	 */
	public function configure(KSchedularTaskConfig $taskConfig)
	{
	}
	
	/**
	 * 
	 * @param KalturaHuluDistributionProfile $distributionProfile
	 * @return sftpMgr
	 */
	protected function getSFTPManager(KalturaHuluDistributionProfile $distributionProfile)
	{
		$serverUrl = $distributionProfile->sftpHost;
		$loginName = $distributionProfile->sftpLogin;
		$loginPass = $distributionProfile->sftpPass;
		$sftpManager = kFileTransferMgr::getInstance(kFileTransferMgrType::SFTP_CMD);
		$sftpManager->login($serverUrl, $loginName, $loginPass);
		return $sftpManager;
	}

}