<?php
/**
 * @package plugins.huluDistribution
 * @subpackage lib
 */
class HuluDistributionEngine extends DistributionEngine implements 
	IDistributionEngineSubmit,
	IDistributionEngineCloseSubmit
{
	
	
	protected $tempFilePath;
	
	const TEMP_DIRECTORY = 'hulu_distribution';
	
	
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
		
		$fileManager = $this->getFileTransferManager($distributionProfile);
		
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
		
		$fileManager->putFile($videoSFTPPath, $videoFilePath);
		
		if($thumbAssetFilePath && file_exists($thumbAssetFilePath))
			$fileManager->putFile($thumbSFTPPath, $thumbAssetFilePath);
			
		$fileManager->filePutContents($xmlSFTPPath, $xml);
	}
	
	/* (non-PHPdoc)
	 * @see DistributionEngine::configure()
	 */
	public function configure(KSchedularTaskConfig $taskConfig)
	{
		if($taskConfig->params->tempFilePath)
		{
			$this->tempFilePath = $taskConfig->params->tempFilePath;
			if(!is_dir($this->tempFilePath))
				kFile::fullMkfileDir($this->tempFilePath, 0777, true);
		}
		else
		{
			$this->tempFilePath = sys_get_temp_dir();
			KalturaLog::info('params.tempFilePath configuration not supplied, using default system directory ['.$this->tempFilePath.']');
		}
	}
	
	/**
	 * 
	 * @param KalturaHuluDistributionProfile $distributionProfile
	 * @return kFileTransferMgr
	 */
	protected function getFileTransferManager(KalturaHuluDistributionProfile $distributionProfile)
	{
		$port = $distributionProfile->port;
		$protocol = $distributionProfile->protocol ?  $distributionProfile->protocol : KalturaDistributionProtocol::SFTP_CMD;
		$privateKey = null;
		switch ($protocol){
			case KalturaDistributionProtocol::ASPERA:
				$host = $distributionProfile->asperaHost;
				$username = $distributionProfile->asperaLogin;
				$password = $distributionProfile->asperaPass;
				$publicKey = $distributionProfile->asperaPublicKey;
				$privateKey = $distributionProfile->asperaPrivateKey;
				$passphrase = $distributionProfile->passphrase;
				break;
			case KalturaDistributionProtocol::SFTP_CMD:
				$host = $distributionProfile->sftpHost;
				$username = $distributionProfile->sftpLogin;
				$password = $distributionProfile->sftpPass;
				break;
		}
		$fileTransferManager = kFileTransferMgr::getInstance($protocol);
		if ($privateKey && trim($privateKey))
		{
			try
			{
				$publicKeyTempPath = $this->getFileLocationForSFTPKey($distributionProfile->id, $publicKey, 'publickey');
				$privateKeyTempPath = $this->getFileLocationForSFTPKey($distributionProfile->id, $privateKey, 'privatekey');
				$fileTransferManager->loginPubKey($host, $username, $publicKeyTempPath, $privateKeyTempPath, $passphrase, ($port) ? $port : null);
			}
			catch(Exception $ex)
			{
				if (file_exists($publicKeyTempPath))
					unlink($publicKeyTempPath);
				if (file_exists($privateKeyTempPath))
					unlink($privateKeyTempPath);
				throw $ex;
			}
		}
		else
		{
			$fileTransferManager->login($host, $username, $password, ($port) ? $port : null);
		}
		return $fileTransferManager;
	}
	
	private function getFileLocationForSFTPKey($distributionProfileId, $keyContent, $fileName) 
	{
		$tempDirectory = $this->getTempDirectoryForProfile($distributionProfileId);
		$fileLocation = $tempDirectory . $fileName;
		if (!file_exists($fileLocation) || (file_get_contents($fileLocation) !== $keyContent))
		{
			file_put_contents($fileLocation, $keyContent);
			chmod($fileLocation, 0600);
		}
		
		return $fileLocation;
	}
	
	private function getTempDirectoryForProfile($distributionProfileId)
	{
		$tempFilePath = kConf::get('temp_folder') . '/' . self::TEMP_DIRECTORY . '/' . $distributionProfileId . '/';
		if (!file_exists($tempFilePath))
			mkdir($tempFilePath, 0777, true);
		return $tempFilePath;
	}

}