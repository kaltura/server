<?php
/**
 * @package plugins.quickPlayDistribution
 * @subpackage lib
 */
class QuickPlayDistributionEngine extends DistributionEngine implements 
	IDistributionEngineSubmit,
	IDistributionEngineUpdate
{
	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		$this->validateJobDataObjectTypes($data);
		
		$this->handleSubmit($data, $data->distributionProfile, $data->providerData);
		
		return true;
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
	 * @param KalturaDistributionJobData $data
	 * @throws Exception
	 */
	protected function validateJobDataObjectTypes(KalturaDistributionJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaQuickPlayDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaQuickPlayDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaQuickPlayDistributionJobProviderData))
			throw new Exception("Provider data must be of type KalturaQuickPlayDistributionJobProviderData");
	}
	
	/**
	 * @param string $path
	 * @param KalturaDistributionJobData $data
	 * @param KalturaVerizonDistributionProfile $distributionProfile
	 * @param KalturaVerizonDistributionJobProviderData $providerData
	 */
	public function handleSubmit(KalturaDistributionJobData $data, KalturaQuickPlayDistributionProfile $distributionProfile, KalturaQuickPlayDistributionJobProviderData $providerData)
	{
		KalturaLog::debug("Submiting data");
		
		$fileName = $data->entryDistribution->entryId . '_' . date('Y-m-d_H-i-s') . '.xml';
		KalturaLog::debug('Sending file '. $fileName);
		die($providerData->xml);
		$ftpManager = $this->getFTPManager($distributionProfile);
		$tmpFile = tmpfile();
		if ($tmpFile === false)
			throw new Exception('Failed to create tmp file');
		fwrite($tmpFile, $providerData->xml);
		rewind($tmpFile);
		$res = ftp_fput($ftpManager->getConnection(), $fileName, $tmpFile, FTP_ASCII);
		fclose($tmpFile);
		
		if ($res === false)
			throw new Exception('Failed to upload tmp file to ftp');
			
		KalturaLog::info('File was sent successfully');
			
		$data->remoteId = $fileName;
		$data->sentData = $providerData->xml;
	}
	
	/* (non-PHPdoc)
	 * @see DistributionEngine::configure()
	 */
	public function configure(KSchedularTaskConfig $taskConfig)
	{
	}
	
	/**
	 * 
	 * @param KalturaQuickPlayDistributionProfile $distributionProfile
	 * @return ftpMgr
	 */
	protected function getFTPManager(KalturaQuickPlayDistributionProfile $distributionProfile)
	{
		$host = $distributionProfile->ftpHost;
		$login = $distributionProfile->ftpLogin;
		$pass = $distributionProfile->ftpPass;
		$ftpManager = kFileTransferMgr::getInstance(kFileTransferMgrType::FTP);
		$ftpManager->login($host, $login, $pass);
		return $ftpManager;
	}
}