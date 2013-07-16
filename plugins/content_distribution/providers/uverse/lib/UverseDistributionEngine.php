<?php
/**
 * @package plugins.uverseDistribution
 * @subpackage lib
 */
class UverseDistributionEngine extends DistributionEngine implements 
	IDistributionEngineUpdate,
	IDistributionEngineSubmit,
	IDistributionEngineDelete
{
	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaUverseDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaUverseDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaUverseDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaUverseDistributionJobProviderData");
		
		$this->sendFile($data, $data->distributionProfile, $data->providerData);
		
		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineDelete::delete()
	 */
	public function delete(KalturaDistributionDeleteJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaUverseDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaUverseDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaUverseDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaUverseDistributionJobProviderData");
		
		$this->handleDelete($data, $data->distributionProfile, $data->providerData);
		
		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineUpdate::update()
	 */
	public function update(KalturaDistributionUpdateJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaUverseDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaUverseDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaUverseDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaUverseDistributionJobProviderData");
		
		$this->sendFile($data, $data->distributionProfile, $data->providerData);
		
		return true;
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaUverseDistributionProfile $distributionProfile
	 * @param KalturaUverseDistributionJobProviderData $providerData
	 */
	protected function sendFile(KalturaDistributionJobData $data, KalturaUverseDistributionProfile $distributionProfile, KalturaUverseDistributionJobProviderData $providerData)
	{
		$ftpManager = $this->getFTPManager($distributionProfile);
		
		$providerData->remoteAssetFileName = $this->getRemoteFileName($distributionProfile, $providerData);
		$providerData->remoteAssetUrl = $this->getRemoteUrl($distributionProfile, $providerData);
		if ($ftpManager->fileExists($providerData->remoteAssetFileName))
			KalturaLog::err('The file ['.$providerData->remoteAssetFileName.'] already exists at the FTP');
		else
			$ftpManager->putFile($providerData->remoteAssetFileName, $providerData->localAssetFilePath);
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaUverseDistributionProfile $distributionProfile
	 * @param KalturaUverseDistributionJobProviderData $providerData
	 */
	protected function handleDelete(KalturaDistributionJobData $data, KalturaUverseDistributionProfile $distributionProfile, KalturaUverseDistributionJobProviderData $providerData)
	{
		$ftpManager = $this->getFTPManager($distributionProfile);
		$ftpManager->delFile($providerData->remoteAssetFileName);
	}
	
	/**
	 * 
	 * @param KalturaUverseDistributionProfile $distributionProfile
	 * @return ftpMgr
	 */
	protected function getFTPManager(KalturaUverseDistributionProfile $distributionProfile)
	{
		$host = $distributionProfile->ftpHost;
		$login = $distributionProfile->ftpLogin;
		$password = $distributionProfile->ftpPassword;
		$engineOptions = isset(KBatchBase::$taskConfig->engineOptions) ? KBatchBase::$taskConfig->engineOptions->toArray() : array();
		$ftpManager = kFileTransferMgr::getInstance(kFileTransferMgrType::FTP, $engineOptions);
		$ftpManager->login($host, $login, $password);
		return $ftpManager;
	}
	
	/**
	 * @param KalturaUverseDistributionProfile $distributionProfile
	 * @param KalturaUverseDistributionJobProviderData $providerData
	 * @return string
	 */
	protected function getRemoteFileName(KalturaUverseDistributionProfile $distributionProfile, KalturaUverseDistributionJobProviderData $providerData)
	{
		return pathinfo($providerData->localAssetFilePath, PATHINFO_BASENAME);
	}
	
	/**
	 * @param KalturaUverseDistributionProfile $distributionProfile
	 * @param KalturaUverseDistributionJobProviderData $providerData
	 * @return string
	 */
	protected function getRemoteUrl(KalturaUverseDistributionProfile $distributionProfile, KalturaUverseDistributionJobProviderData $providerData)
	{
		$remoteFileName = $this->getRemoteFileName($distributionProfile, $providerData);
		return 'ftp://'.$distributionProfile->ftpHost.'/'.$remoteFileName;
	}
}