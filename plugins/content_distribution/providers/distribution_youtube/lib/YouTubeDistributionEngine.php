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
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaYouTubeDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaYouTubeDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaYouTubeDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaYouTubeDistributionJobProviderData");
		
		$this->handleSubmit($data, $data->distributionProfile, $data->providerData);
		
		return false;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		$statusXml = $this->fetchStatusXml($data, $data->distributionProfile, $data->providerData);

		if ($statusXml === false) // no status yet
			return false;
			
		$statusParser = new YouTubeDistributionStatusParser($statusXml);
		$status = $statusParser->getStatusForCommand('Insert');
		$statusDetail = $statusParser->getStatusDetailForCommand('Insert');
		if (is_null($status))
		{
			// try to get the status of Parse command
			$status = $statusParser->getStatusForCommand('Parse');
			$statusDetail = $statusParser->getStatusDetailForCommand('Parse');
			if (!is_null($status))
				throw new KalturaDistributionException('Distribution failed on parsing command with status ['.$status.'] and error ['.$statusDetail.']');
			else
				throw new KalturaDistributionException('Status could not be found after distribution submission');
		}
		
		if ($status != 'Success')
			throw new KalturaDistributionException('Distribution failed with status ['.$status.'] and error ['.$statusDetail.']');
			
		$remoteId = $statusParser->getRemoteId();
		if (is_null($remoteId))
			throw new KalturaDistributionException('Remote id was not found after distribution submission');
		
		$data->remoteId = $remoteId;
			
		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineDelete::delete()
	 */
	public function delete(KalturaDistributionDeleteJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaYouTubeDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaYouTubeDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaYouTubeDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaYouTubeDistributionJobProviderData");
		
		$this->handleDelete($data, $data->distributionProfile, $data->providerData);
		
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseDelete::closeDelete()
	 */
	public function closeDelete(KalturaDistributionDeleteJobData $data)
	{
		$statusXml = $this->fetchStatusXml($data, $data->distributionProfile, $data->providerData);

		if ($statusXml === false) // no status yet
			return false;
			
		$statusParser = new YouTubeDistributionStatusParser($statusXml);
		$status = $statusParser->getStatusForCommand('Delete');
		$statusDetail = $statusParser->getStatusDetailForCommand('Delete');
		if (is_null($status))
			throw new KalturaDistributionException('Status could not be found after deletion request');
		
		if ($status != 'Success')
			throw new KalturaDistributionException('Delete failed with status ['.$status.'] and error ['.$statusDetail.']');
			
		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineUpdate::update()
	 */
	public function update(KalturaDistributionUpdateJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaYouTubeDistributionProfile))
			KalturaLog::err("Distribution profile must be of type KalturaYouTubeDistributionProfile");
	
		if(!$data->providerData || !($data->providerData instanceof KalturaYouTubeDistributionJobProviderData))
			KalturaLog::err("Provider data must be of type KalturaYouTubeDistributionJobProviderData");
		
		$this->handleUpdate($data, $data->distributionProfile, $data->providerData);
		
		return false;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseUpdate::closeUpdate()
	 */
	public function closeUpdate(KalturaDistributionUpdateJobData $data)
	{
		$statusXml = $this->fetchStatusXml($data, $data->distributionProfile, $data->providerData);

		if ($statusXml === false) // no status yet
			return false;
			
		$statusParser = new YouTubeDistributionStatusParser($statusXml);
		$status = $statusParser->getStatusForCommand('Update');
		$statusDetail = $statusParser->getStatusDetailForCommand('Update');
		if (is_null($status))
			throw new KalturaDistributionException('Status could not be found after distribution update');
		
		if ($status != 'Success')
			throw new KalturaDistributionException('Update failed with status ['.$status.'] and error ['.$statusDetail.']');
			
		return true;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineReport::fetchReport()
	 */
	public function fetchReport(KalturaDistributionFetchReportJobData $data)
	{
		return false;
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaYouTubeDistributionProfile $distributionProfile
	 * @param KalturaYouTubeDistributionJobProviderData $providerData
	 */
	protected function handleSubmit(KalturaDistributionJobData $data, KalturaYouTubeDistributionProfile $distributionProfile, KalturaYouTubeDistributionJobProviderData $providerData)
	{
		$entryId = $data->entryDistribution->entryId;
		$entry = $this->kalturaClient->media->get($entryId);
		
		$videoFileFile = $providerData->videoAssetFilePath;
		if (!file_exists($videoFileFile))
			throw new Exception('The file ['.$videoFileFile.'] was not found for YouTube distribution');
		
		$feed = new YouTubeDistributionFeedHelper(self::FEED_TEMPLATE, $distributionProfile, $data->entryDistribution);
		$feed->setAction('Insert');
		$feed->setMetadataFromEntry($entry);
		$feed->setContentUrl('file://' . pathinfo($videoFileFile, PATHINFO_BASENAME));
		
		$sftpManager = $this->getSFTPManager($distributionProfile);
		
		$feed->sendFeed($sftpManager);
		
		// upload the video
		$videoSFTPPath = $feed->getDirectoryName() . '/' . pathinfo($videoFileFile, PATHINFO_BASENAME);
		$sftpManager->putFile($videoSFTPPath, $videoFileFile);
		
		$feed->setDeliveryComplete($sftpManager);
		
		$providerData->sftpDirectory = $feed->getDirectoryName();
		$providerData->sftpMetadataFilename = $feed->getMetadataTempFileName();
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaYouTubeDistributionProfile $distributionProfile
	 * @param KalturaYouTubeDistributionJobProviderData $providerData
	 */
	protected function handleDelete(KalturaDistributionJobData $data, KalturaYouTubeDistributionProfile $distributionProfile, KalturaYouTubeDistributionJobProviderData $providerData)
	{
		$feed = new YouTubeDistributionFeedHelper(self::FEED_TEMPLATE, $distributionProfile, $data->entryDistribution);
		$feed->setAction('Delete');
		$feed->setVideoId($data->remoteId);
		
		$sftpManager = $this->getSFTPManager($distributionProfile);
		
		$feed->sendFeed($sftpManager);
		$feed->setDeliveryComplete($sftpManager);
		
		$providerData->sftpDirectory = $feed->getDirectoryName();
		$providerData->sftpMetadataFilename = $feed->getMetadataTempFileName();
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaYouTubeDistributionProfile $distributionProfile
	 * @param KalturaYouTubeDistributionJobProviderData $providerData
	 */
	protected function handleUpdate(KalturaDistributionJobData $data, KalturaYouTubeDistributionProfile $distributionProfile, KalturaYouTubeDistributionJobProviderData $providerData)
	{
		$entryId = $data->entryDistribution->entryId;
		$entry = $this->kalturaClient->media->get($entryId);
		
		$feed = new YouTubeDistributionFeedHelper(self::FEED_TEMPLATE, $distributionProfile, $data->entryDistribution);
		$feed->setAction('Update');
		$feed->setVideoId($data->remoteId);
		$feed->setMetadataFromEntry($entry);
		
		$sftpManager = $this->getSFTPManager($distributionProfile);
		
		$feed->sendFeed($sftpManager);
		$feed->setDeliveryComplete($sftpManager);
		
		$providerData->sftpDirectory = $feed->getDirectoryName();
		$providerData->sftpMetadataFilename = $feed->getMetadataTempFileName();
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaYouTubeDistributionProfile $distributionProfile
	 * @param KalturaYouTubeDistributionJobProviderData $providerData
	 * @return Status XML or FALSE when status is not available yet
	 */
	protected function fetchStatusXml(KalturaDistributionJobData $data, KalturaYouTubeDistributionProfile $distributionProfile, KalturaYouTubeDistributionJobProviderData $providerData)
	{
		$statusFilePath = $providerData->sftpDirectory . '/' . 'status-' . $providerData->sftpMetadataFilename;
		$sftpManager = $this->getSFTPManager($distributionProfile);
		$statusXml = null;
		try 
		{
			KalturaLog::info('Trying to get the following status file: ['.$statusFilePath.']');
			$statusXml = $sftpManager->fileGetContents($statusFilePath);
		}
		catch(kFileTransferMgrException $ex) // file is still missing
		{
			KalturaLog::info('File doesn\'t exists yet, retry later');
			return false;
		}
		
		KalturaLog::info('Status file was found');
		
		return $statusXml;
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
	
	/*
	 * Creates and return the temp directory used for this distribution profile 
	 */
	protected function getTempDirectoryForProfile($distributionProfileId)
	{
		$metadataTempFilePath = kConf::get('temp_folder') . '/' . self::TEMP_DIRECTORY . '/'  . $distributionProfileId . '/';
		if (!file_exists($metadataTempFilePath))
			mkdir($metadataTempFilePath, 0777, true);
		return $metadataTempFilePath;
	}
}