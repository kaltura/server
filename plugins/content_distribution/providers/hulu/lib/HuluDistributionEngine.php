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
		
		//If file is remote fetch it first to local
		list($isRemoteVideo, $videoRemoteUrl) = kFile::resolveFilePath($providerData->videoAssetFilePath);
		$videoFilePath = !$isRemoteVideo ? $providerData->videoAssetFilePath : kFile::getExternalFile($thumbRemoteUrl, null, basename($providerData->videoAssetFilePath));
		
		$thumbAssetFilePath = $providerData->thumbAssetFilePath;
		if($thumbAssetFilePath)
		{
			list($isRemoteThumb, $thumbRemoteUrl) = kFile::resolveFilePath($thumbAssetFilePath);
			$thumbAssetFilePath = !$isRemoteThumb ? $thumbAssetFilePath : kFile::getExternalFile($thumbRemoteUrl, null, basename($thumbAssetFilePath));
		}
		
		$captionsFilesPaths = $providerData->captionLocalPaths;
		foreach ($captionsFilesPaths as &$captionFilePath)
		{
			list($isRemoteCaption, $captionRemoteUrl) = kFile::resolveFilePath($captionFilePath->value);
			$captionFilePath->localFilePath = !$isRemoteCaption ? $captionFilePath->value : kFile::getExternalFile($thumbRemoteUrl, null, basename($captionRemoteUrl));
			$captionFilePath->isRemoteCaption = $isRemoteCaption;
		}
		
		$protocol = $distributionProfile->protocol ? $distributionProfile->protocol : KalturaDistributionProtocol::SFTP;
		$remoteVideoFileName = $providerData->fileBaseName.'.'.pathinfo($providerData->videoAssetFilePath, PATHINFO_EXTENSION);
		$remoteThumbFileName = $providerData->fileBaseName.'.'.pathinfo($providerData->thumbAssetFilePath, PATHINFO_EXTENSION);
		$remoteXmlFileName = $providerData->fileBaseName.'.xml';
		switch ($protocol)
		{
			case KalturaDistributionProtocol::SFTP:
				$sftpBasePath = '/home/' . $distributionProfile->sftpLogin . '/upload';
				$videoSFTPPath = $sftpBasePath.'/'.$remoteVideoFileName;
				$thumbSFTPPath = $sftpBasePath.'/'.$remoteThumbFileName;
				$xmlSFTPPath = $sftpBasePath.'/'.$remoteXmlFileName;
				KalturaLog::info('$videoSFTPPath:' . $videoSFTPPath);
				KalturaLog::info('$thumbSFTPPath:' . $thumbSFTPPath);
				KalturaLog::info('$xmlSFTPPath:' . $xmlSFTPPath);
				KalturaLog::info('XML:' . $xml);
				$fileManager = $this->getSFTPManager($distributionProfile);
				$fileManager->putFile($videoSFTPPath, $videoFilePath);
				if($thumbAssetFilePath && kFile::checkFileExists($thumbAssetFilePath))
					$fileManager->putFile($thumbSFTPPath, $thumbAssetFilePath);
					
				foreach ($captionsFilesPaths as $captionFilePath)
				{
					if(kFile::checkFileExists($captionFilePath->value))
					{
						$remoteCaptionFileName = $providerData->fileBaseName.'.'.pathinfo($captionFilePath->value, PATHINFO_EXTENSION);
						$captionSFTPPath = $sftpBasePath.'/'.$remoteCaptionFileName;
						KalturaLog::info('$captionSFTPPath:' . $captionSFTPPath);
						$fileManager->putFile($captionSFTPPath, $captionFilePath->localFilePath);
					}
				}
				$fileManager->filePutContents($xmlSFTPPath, $xml);
				break;
				
			case KalturaDistributionProtocol::ASPERA:
				$xmlTempPath = $this->getFileLocation($distributionProfile->id, $xml, $providerData->fileBaseName.'.xml');
				$host = $distributionProfile->asperaHost;
				$username = $distributionProfile->asperaLogin;
				$password = $distributionProfile->asperaPass;
				$privateKey = $distributionProfile->asperaPrivateKey;
				$passphrase = $distributionProfile->passphrase;
				$port = $distributionProfile->port;
				$privateKeyTempPath = null;
				if (trim($privateKey))
				{
					$privateKeyTempPath = $this->getFileLocation($distributionProfile->id, $privateKey, 'privatekey');
				}
				if ($videoFilePath && kFile::checkFileExists($videoFilePath))
				{
					$this->uploadFileWithAspera($host, $username, $videoFilePath, $password, $privateKeyTempPath, $passphrase, $port, $remoteVideoFileName);
				}
				else
				{
					throw new kFileTransferMgrException("video file [$videoFilePath] not exists", kFileTransferMgrException::localFileNotExists);
				}
				if($thumbAssetFilePath && kFile::checkFileExists($thumbAssetFilePath))
				{
					$this->uploadFileWithAspera($host, $username, $thumbAssetFilePath, $password, $privateKeyTempPath, $passphrase, $port, $remoteThumbFileName );
				}
				foreach ($captionsFilesPaths as $captionFilePath)
				{
					if(kFile::checkFileExists($captionFilePath->localFilePath))
					{
						$remoteCaptionFileName = $providerData->fileBaseName.'.'.pathinfo($captionFilePath->value, PATHINFO_EXTENSION);
						$this->uploadFileWithAspera($host, $username, $captionFilePath->localFilePath, $password, $privateKeyTempPath, $passphrase, $port, $remoteCaptionFileName);
					}
				}
				if($xmlTempPath && kFile::checkFileExists($xmlTempPath))
				{
					$this->uploadFileWithAspera($host, $username, $xmlTempPath, $password, $privateKeyTempPath, $passphrase, $port, $remoteXmlFileName);
				}
				break;
		}
		
		$this->deleteTempFiles($isRemoteVideo, $videoFilePath, $isRemoteThumb, $thumbAssetFilePath, $captionsFilesPaths);
	}
	
	private function getFileLocation($distributionProfileId, $content, $fileName) 
	{
		$tempDirectory = $this->getTempDirectoryForProfile($distributionProfileId);
		$fileLocation = $tempDirectory . $fileName;
		if (!kFile::checkFileExists($fileLocation) || (kFile::getFileContent($fileLocation) !== $content))
		{
			kFile::filePutContents($fileLocation, $content);
			kFile::chmod($fileLocation, 0600);
		}
		
		return $fileLocation;
	}
	
	private function getTempDirectoryForProfile($distributionProfileId)
	{
		$tempFilePath = $this->tempDirectory . '/' . self::TEMP_DIRECTORY . '/' . $distributionProfileId . '/';
		if (!kFile::checkFileExists($tempFilePath))
			kFile::mkdir($tempFilePath, 0777, true);
		return $tempFilePath;
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
		$engineOptions = isset(KBatchBase::$taskConfig->engineOptions) ? KBatchBase::$taskConfig->engineOptions->toArray() : array();
		$sftpManager = kFileTransferMgr::getInstance(kFileTransferMgrType::SFTP, $engineOptions);
		$sftpManager->login($serverUrl, $loginName, $loginPass);
		return $sftpManager;
	}
	
	private function uploadFileWithAspera($host, $username, $filePath, $password = null, $privateKeyTempPath = null, $passphrase = null, $port = 22, $remoteFilePath = ''){
		
		$this->validateParameters($host, $username, $filePath, $password, $privateKeyTempPath, $passphrase, $port, $remoteFilePath);
				
		$remoteFilePath = ltrim($remoteFilePath,'/');
		$cmd= $this->getCmdPrefix($privateKeyTempPath, $passphrase, $password, $port);
		$cmd.=" $filePath $username@$host:'$remoteFilePath'";
		$res = $this->executeCmd($cmd);
		if (!$res){
			$last_error = error_get_last();
			throw new KalturaDistributionException("Can't put file [$remoteFilePath] - " . $last_error['message'], kFileTransferMgrException::otherError);
		}
	}
	
	private function validateParameters($host, $username, $filePath, $password = null, $privateKeyTempPath = null, $passphrase = null, $port = 22, $remoteFilePath = '') {
		
		$VALID_HOSTNAME_PATTERN = "/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\\-]*[a-zA-Z0-9])\\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\\-]*[A-Za-z0-9])$/";
		$VALID_USERNAME_PATTERN = "/^([a-z_][a-z0-9_]{0,30})$/";
		
		$validInput = TRUE;
		$validInput &= (preg_match ($VALID_HOSTNAME_PATTERN, $host) === 1); // $host
		$validInput &= (preg_match ($VALID_USERNAME_PATTERN, $username) === 1); // $username
		// $filePath : No need in validation, Checked by the callee
		$validInput &= (is_null($password)) || (strpos($password, "'") === FALSE); // $password : can't contain ' 
		// $privateKeyTempPath: No need in validation, inner parameter
		$validInput &= (is_null($passphrase)) || (strpos($passphrase, "'") === FALSE);// $passphrase : can't contain '
		$validInput &= is_numeric($port); // $port 
		// $remoteFilePath : No need in validation, inner parameter with validation in creation.
		
		if(!$validInput)
			throw new kFileTransferMgrException("Can't put file, Illegal parameters");
	}
	
	private function getCmdPrefix($privateKeyTempPath, $passphrase, $password, $port){
		$cmd = '';
		if ($privateKeyTempPath){
			if ($passphrase)
				$cmd = "(echo '$passphrase') | ascp ";
			else  
				$cmd = "ascp ";
		}
		else 
			$cmd = "(echo '$password') | ascp ";
		$cmd.=" -P $port ";
		
		//when connecting to a remote host and prompted to accept a host key, ascp ignores the request
		$cmd.=" --ignore-host-key ";
		if ($privateKeyTempPath)
			$cmd.=" -i $privateKeyTempPath ";
		return $cmd;
		
	}
	
	private function executeCmd($cmd){
		KalturaLog::info('Executing command: '.$cmd);
		$return_value = null;
		$beginTime = time();
		system($cmd, $return_value);
		$duration = (time() - $beginTime)/1000;
		KalturaLog::info("Execution took [$duration]sec with value [$return_value]");
		if ($return_value == 0)
			return true;
		return false;
	}

	private function deleteTempFiles($isRemoteVideo, $videoFilePath, $isRemoteThumb, $thumbAssetFilePath, $captionsFilesPaths)
	{
		if($isRemoteVideo)
		{
			kFile::unlink($videoFilePath);
		}
		
		if($isRemoteThumb)
		{
			kFile::unlink($thumbAssetFilePath);
		}
		
		foreach ($captionsFilesPaths as $captionsFilesPath)
		{
			if(isset($captionsFilesPaths->isRemoteCaption) && $captionsFilesPaths->isRemoteCaption)
			{
				kFile::unlink($captionFilePath->localFilePath);
			}
		}
	}
}