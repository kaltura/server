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
        if (trim($privateKey))
        {
            $publicKeyTempPath = $this->tempFilePath . '/' . uniqid(null, true);
            $privateKeyTempPath = $this->tempFilePath . '/' . uniqid(null, true);
            try
            {
                file_put_contents($publicKeyTempPath, $publicKey);
                file_put_contents($privateKeyTempPath, $privateKey);
                $fileTransferManager->loginPubKey($host, $username, $publicKeyTempPath, $privateKeyTempPath, $passphrase, ($port) ? $port : null);
                unlink($publicKeyTempPath);
                unlink($privateKeyTempPath);
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
            KalturaLog::debug("here");
        }
		return $fileTransferManager;
	}

}