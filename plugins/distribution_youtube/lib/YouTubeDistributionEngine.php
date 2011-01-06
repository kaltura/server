<?php
class YouTubeDistributionEngine extends DistributionEngine implements 
	IDistributionEngineUpdate,
	IDistributionEngineSubmit,
	IDistributionEngineReport,
	IDistributionEngineDelete,
	IDistributionEngineCloseUpdate,
	IDistributionEngineCloseSubmit,
	IDistributionEngineCloseDelete
{
	const TEMP_DIRECTORY = 'youtube_distribution';

	
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
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaYouTubeDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaYouTubeDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaYouTubeDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaYouTubeDistributionJobProviderData");
		
		$this->handleSend($data, $data->distributionProfile, $data->providerData);
		
		return true;
	}

	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaYouTubeDistributionProfile $distributionProfile
	 * @param KalturaYouTubeDistributionJobProviderData $providerData
	 * @throws Exception
	 */
	public function handleSend(KalturaDistributionJobData $data, KalturaYouTubeDistributionProfile $distributionProfile, KalturaYouTubeDistributionJobProviderData $providerData)
	{
		$timestampName = date('Ymd-His') . '_' . time();
		$metadataTempFileName = 'youtube_' . $timestampName . '.xml';
		$notificationEmail = $distributionProfile->notificationEmail;
		$username = $distributionProfile->username;
		$entryId = $data->entryDistribution->entryId;
		$entry = $this->kalturaClient->media->get($entryId);
		$metadataTemplate = realpath(dirname(__FILE__) . '/../') . '/xml/metadata_template.xml';
		$deliveryCompleteFile = realpath(dirname(__FILE__) . '/../') . '/xml/delivery.complete';
		$videoFileFile = $providerData->videoAssetFilePath;
		if (!file_exists($videoFileFile))
			throw new Exception('The file ['.$videoFileFile.'] was not found for YouTube distribution');
		
		$metadataTempFilePath = $this->getTempDirectoryForProfile($distributionProfile->id);
		$metadataTempFilePath = $metadataTempFilePath . $metadataTempFileName;
		
		// prepare the metadata
		$doc = new DOMDocument();
		$doc->load($metadataTemplate);
		
		$xpath = new DOMXPath($doc);
		$xpath->registerNamespace('media', 'http://search.yahoo.com/mrss');
		$xpath->registerNamespace('yt', 'http://www.youtube.com/schemas/yt/0.2');
		
		$notificationEmailNode = $xpath->query('/rss/channel/yt:notification_email')->item(0);
		$userNameNode = $xpath->query('/rss/channel/yt:account/yt:username')->item(0);
		$titleNode = $xpath->query('/rss/channel/item/media:title')->item(0)->childNodes->item(0);
		$descriptionNode = $xpath->query('/rss/channel/item/media:content/media:description')->item(0)->childNodes->item(0);
		$keywordsNode = $xpath->query('/rss/channel/item/media:content/media:keywords')->item(0)->childNodes->item(0);
		$fileNameNode = $xpath->query('/rss/channel/item/media:content/@url')->item(0);
		
		$notificationEmailNode->nodeValue = $notificationEmail;
		$userNameNode->nodeValue = $username;
		$titleNode->nodeValue = $entry->name;
		$descriptionNode->nodeValue = $entry->description;
		$keywordsNode->nodeValue = $entry->tags;
		$fileNameNode->nodeValue = 'file://' . pathinfo($videoFileFile, PATHINFO_BASENAME);
		
		$doc->save($metadataTempFilePath);
		
		$sftpManager = $this->getSFTPManager($distributionProfile);
		
		$directoryName = '/' . $timestampName;
		
		// upload the metadata
		$sftpManager->putFile($directoryName . '/' . $metadataTempFileName, $metadataTempFilePath);
		
		// upload the video
		$sftpManager->putFile($directoryName . '/' . pathinfo($videoFileFile, PATHINFO_BASENAME), $videoFileFile);
		
		// upload the delivery.complete marker file
		$sftpManager->putFile($directoryName . '/' . pathinfo($deliveryCompleteFile, PATHINFO_BASENAME), $deliveryCompleteFile);
		
		$providerData->sftpDirectory = $directoryName;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		$publishState = $this->fetchStatusXml($data, $data->distributionProfile, $data->providerData);
		return false;
		// parse
	}

	/**
	 * @param $data
	 * @param $distributionProfile
	 * @param $providerData
	 */
	public function fetchStatusXml(KalturaDistributionJobData $data, KalturaYouTubeDistributionProfile $distributionProfile, KalturaYouTubeDistributionJobProviderData $providerData)
	{
		$statusFilePath = $providerData->sftpDirectory . '/' . 'status-' . $providerData->sftpMetadataFilename;
		$sftpManager = $this->getSFTPManager($distributionProfile);
		$sftpManager->getFile()
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineDelete::delete()
	 */
	public function delete(KalturaDistributionDeleteJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaMsnDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaMsnDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaMsnDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaMsnDistributionJobProviderData");
		
		$this->handleSend($this->deletePath, $data, $data->distributionProfile, $data->providerData);
		
		return false;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseDelete::closeDelete()
	 */
	public function closeDelete(KalturaDistributionDeleteJobData $data)
	{
		$publishState = $this->fetchStatus($data);
		
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseUpdate::closeUpdate()
	 */
	public function closeUpdate(KalturaDistributionUpdateJobData $data)
	{
		$publishState = $this->fetchStatus($data);

	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineReport::fetchReport()
	 */
	public function fetchReport(KalturaDistributionFetchReportJobData $data)
	{
		return false;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineUpdate::update()
	 */
	public function update(KalturaDistributionUpdateJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaMsnDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaMsnDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaMsnDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaMsnDistributionJobProviderData");
		
		$this->handleSend($this->deletePath, $data, $data->distributionProfile, $data->providerData);
		
		return false;
	}
	
	/**
	 * 
	 * @param KalturaYouTubeDistributionProfile $distributionProfile
	 * @return sftpMgr
	 */
	protected function getSFTPManager(KalturaYouTubeDistributionProfile $distributionProfile)
	{
		$serverUrl = $distributionProfile->sftpHost;
		$loginName = $distributionProfile->sftpLogin;
		$publicKeyFile = $this->getFileLocationForSFTPKey($distributionProfile->id, $distributionProfile->sftpPublicKey, 'publickey');
		$privateKeyFile = $this->getFileLocationForSFTPKey($distributionProfile->id, $distributionProfile->sftpPrivateKey, 'privatekey');
		$sftpManager = kFileTransferMgr::getInstance(kFileTransferMgrType::SFTP);
		$sftpManager->loginPubKey($serverUrl, $loginName, $publicKeyFile, $privateKeyFile);
		return $sftpManager;
	}
	
	/*
	 * Creates and return the temp directory used for this distribution profile 
	 */
	protected function getTempDirectoryForProfile($distributionProfileId)
	{
		$metadataTempFilePath = kConf::get('temp_folder') . '/' . YouTubeDistributionEngine::TEMP_DIRECTORY . '/'  . $distributionProfileId . '/';
		if (!file_exists($metadataTempFilePath))
			mkdir($metadataTempFilePath, 0777, true);
		return $metadataTempFilePath;
	}
	
	/*
	 * Lazy saving of the key to a temporary path, the key will exist in this location until the temp files are purged 
	 */
	protected function getFileLocationForSFTPKey($distributionProfileId, $keyContent, $fileName) 
	{
		$tempDirectory = $this->getTempDirectoryForProfile($distributionProfileId);
		$fileLocation = $tempDirectory . $fileName;
		if (!file_exists($fileLocation))
		{
			file_put_contents($fileLocation, $keyContent);
		}
		else
		{
			if (file_get_contents($fileLocation) !== $keyContent) // if key was updated
			{
				file_put_contents($fileLocation, $keyContent);
			}
		}
		
		return $fileLocation;
	}
}