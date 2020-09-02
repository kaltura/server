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
		$fileName = $data->entryDistribution->entryId . '_' . date('Y-m-d_H-i-s') . '.xml';
		KalturaLog::info('Sending file '. $fileName);
		
		$sftpManager = $this->getSFTPManager($distributionProfile);
		
		// upload the thumbnails
		foreach($providerData->thumbnailFilePaths as $thumbnailFilePath)
		{
			/* @var $thumbnailFilePath KalturaString */
			if (!kFile::checkFileExists($thumbnailFilePath->value))
				throw new KalturaDistributionException('Thumbnail file path ['.$thumbnailFilePath.'] not found, assuming it wasn\'t synced and the job will retry');

			$thumbnailUploadPath = '/'.$distributionProfile->sftpBasePath.'/'.pathinfo($thumbnailFilePath->value, PATHINFO_BASENAME);
			if ($sftpManager->fileExists($thumbnailUploadPath))
				KalturaLog::info('File "'.$thumbnailUploadPath.'" already exists, skip it');
			else
				$sftpManager->putFile($thumbnailUploadPath, $thumbnailFilePath->value);
		}
		
		// upload the video files
		foreach($providerData->videoFilePaths as $videoFilePath)
		{
			/* @var $videoFilePath KalturaString */
			if (!kFile::checkFileExists($videoFilePath->value))
				throw new KalturaDistributionException('Video file path ['.$videoFilePath.'] not found, assuming it wasn\'t synced and the job will retry');

			$videoUploadPath = '/'.$distributionProfile->sftpBasePath.'/'.pathinfo($videoFilePath->value, PATHINFO_BASENAME);
			if ($sftpManager->fileExists($videoUploadPath))
				KalturaLog::info('File "'.$videoUploadPath.'" already exists, skip it');
			else
				$sftpManager->putFile($videoUploadPath, $videoFilePath->value);
		}

		$tmpfile = tempnam(sys_get_temp_dir(), time());
		file_put_contents($tmpfile, $providerData->xml);
		// upload the metadata file
		$res = $sftpManager->putFile('/'.$distributionProfile->sftpBasePath.'/'.$fileName, $tmpfile);
		unlink($tmpfile);
				
		if ($res === false)
			throw new Exception('Failed to upload metadata file to sftp');
			
		$data->remoteId = $fileName;
		$data->sentData = $providerData->xml;
	}
	
	/**
	 * 
	 * @param KalturaQuickPlayDistributionProfile $distributionProfile
	 * @return sftpMgr
	 */
	protected function getSFTPManager(KalturaQuickPlayDistributionProfile $distributionProfile)
	{
		$host = $distributionProfile->sftpHost;
		$login = $distributionProfile->sftpLogin;
		$pass = $distributionProfile->sftpPass;
		$engineOptions = isset(KBatchBase::$taskConfig->engineOptions) ? KBatchBase::$taskConfig->engineOptions->toArray() : array();
		$sftpManager = kFileTransferMgr::getInstance(kFileTransferMgrType::SFTP, $engineOptions);
		$sftpManager->login($host, $login, $pass);
		return $sftpManager;
	}
}