<?php
/**
 * @package plugins.youTubeDistribution
 * @subpackage lib
 */
class YouTubeDistributionRightsFeedEngine extends DistributionEngine implements
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

	/**
	 * @var sftpMgr
	 */
	protected $_sftpManager;

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
		{
			// try to get batch status xml to see if there is an internal error on youtube's batch
			$batchStatus = $this->fetchBatchStatus($data, $data->distributionProfile, $data->providerData);
			if ($batchStatus)
				throw new Exception('Internal failure on YouTube, internal_failure-status.xml was found. Error ['.$batchStatus.']');

			return false; // return false to recheck again on next job closing iteration
		}
			
		$statusParser = new YouTubeDistributionRightsFeedLegacyStatusParser($statusXml);
		$status = $statusParser->getStatusForAction('Submit reference');

		// maybe we didn't submit a reference, so let's check the file status
		if (!$status)
			$status = $statusParser->getStatusForAction('Process file');

		if ($status != 'Success')
		{
			$errors = $statusParser->getErrorsSummary();
			throw new Exception('Distribution failed with status ['.$status.'] and errors ['.implode(',', $errors).']');
		}
			
		$referenceId = $statusParser->getReferenceId();
		$assetId = $statusParser->getAssetId();
		$videoId = $statusParser->getVideoId();

		$remoteIdHandler = new YouTubeDistributionRemoteIdHandler();
		$remoteIdHandler->setVideoId($videoId);
		$remoteIdHandler->setAssetId($assetId);
		$remoteIdHandler->setReferenceId($referenceId);
		$data->remoteId = $remoteIdHandler->getSerialized();

		$providerData = $data->providerData;
		$newPlaylists = $this->syncPlaylists($videoId, $providerData);
		$providerData->currentPlaylists = $newPlaylists;
			
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
			
		$statusParser = new YouTubeDistributionRightsFeedLegacyStatusParser($statusXml);
		$status = $statusParser->getStatusForAction('Remove video');
		if (is_null($status))
			throw new Exception('Status could not be found after deletion request');
		
		if ($status != 'Success')
			throw new Exception('Delete failed with status ['.$status.']');
			
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
			
		$statusParser = new YouTubeDistributionRightsFeedLegacyStatusParser($statusXml);
		$status = $statusParser->getStatusForAction('Update video');
		if (is_null($status))
			throw new Exception('Status could not be found after distribution update');
		
		if ($status != 'Success')
			throw new Exception('Update failed with status ['.$status.']');

		$remoteIdHandler = YouTubeDistributionRemoteIdHandler::initialize($data->remoteId);
		$videoId = $remoteIdHandler->getVideoId();

		$providerData = $data->providerData;
		$newPlaylists = $this->syncPlaylists($videoId, $providerData);
		$providerData->currentPlaylists = implode(',',$newPlaylists);

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
		$videoFilePath = $providerData->videoAssetFilePath;
		$thumbnailFilePath = $providerData->thumbAssetFilePath;
		if (!$videoFilePath)
			throw new KalturaDistributionException('No video asset to distribute, the job will fail');

		if (!file_exists($videoFilePath))
			throw new KalturaDistributionException('The file ['.$videoFilePath.'] was not found (probably not synced yet), the job will retry');

		$sftpManager = $this->getSFTPManager($distributionProfile);
		$sftpManager->filePutContents($providerData->sftpDirectory.'/'.$providerData->sftpMetadataFilename, $providerData->submitXml);
		$data->sentData = $providerData->submitXml;
		$data->results = 'none'; // otherwise kContentDistributionFlowManager won't save sentData

		// upload the video
		$videoSFTPPath = $providerData->sftpDirectory.'/'.pathinfo($videoFilePath, PATHINFO_BASENAME);
		$sftpManager->putFile($videoSFTPPath, $videoFilePath);

		// upload the thumbnail if exists
		if (file_exists($thumbnailFilePath))
		{
			$thumbnailSFTPPath = $providerData->sftpDirectory.'/'.pathinfo($thumbnailFilePath, PATHINFO_BASENAME);
			$sftpManager->putFile($thumbnailSFTPPath, $thumbnailFilePath);
		}

		$this->setDeliveryComplete($sftpManager, $providerData->sftpDirectory);
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaYouTubeDistributionProfile $distributionProfile
	 * @param KalturaYouTubeDistributionJobProviderData $providerData
	 */
	protected function handleDelete(KalturaDistributionJobData $data, KalturaYouTubeDistributionProfile $distributionProfile, KalturaYouTubeDistributionJobProviderData $providerData)
	{
		$sftpManager = $this->getSFTPManager($distributionProfile);
		$sftpManager->filePutContents($providerData->sftpDirectory.'/'.$providerData->sftpMetadataFilename, $providerData->deleteXml);
		$data->sentData = $providerData->deleteXml;
		$data->results = 'none'; // otherwise kContentDistributionFlowManager won't save sentData

		$this->setDeliveryComplete($sftpManager, $providerData->sftpDirectory);
	}
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaYouTubeDistributionProfile $distributionProfile
	 * @param KalturaYouTubeDistributionJobProviderData $providerData
	 */
	protected function handleUpdate(KalturaDistributionJobData $data, KalturaYouTubeDistributionProfile $distributionProfile, KalturaYouTubeDistributionJobProviderData $providerData)
	{
		$thumbnailFilePath = $providerData->thumbAssetFilePath;

		$sftpManager = $this->getSFTPManager($distributionProfile);
		$sftpManager->filePutContents($providerData->sftpDirectory.'/'.$providerData->sftpMetadataFilename, $providerData->updateXml);
		$data->sentData = $providerData->updateXml;
		$data->results = 'none'; // otherwise kContentDistributionFlowManager won't save sentData

		// upload the thumbnail if exists
		if (file_exists($thumbnailFilePath))
		{
			$thumbnailSFTPPath = $providerData->sftpDirectory.'/'.pathinfo($thumbnailFilePath, PATHINFO_BASENAME);
			$sftpManager->putFile($thumbnailSFTPPath, $thumbnailFilePath);
		}

		$this->setDeliveryComplete($sftpManager, $providerData->sftpDirectory);
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
			$statusXml = $sftpManager->getFile($statusFilePath);
		}
		catch(kFileTransferMgrException $ex) // file is still missing
		{
			KalturaLog::info('File doesn\'t exist yet, retry later');
			return false;
		}

		KalturaLog::info('Status file was found');

		$data->results = $statusXml;
		return $statusXml;
	}

	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaYouTubeDistributionProfile $distributionProfile
	 * @param KalturaYouTubeDistributionJobProviderData $providerData
	 * @return string Status XML or FALSE when status is not available yet
	 */
	protected function fetchBatchStatus(KalturaDistributionJobData $data, KalturaYouTubeDistributionProfile $distributionProfile, KalturaYouTubeDistributionJobProviderData $providerData)
	{
		$statusFilePath = $providerData->sftpDirectory . '/internal_failure-status.xml';
		$sftpManager = $this->getSFTPManager($distributionProfile);
		$statusXml = null;
		try
		{
			KalturaLog::info('Trying to get the following status file: ['.$statusFilePath.']');
			$statusXml = $sftpManager->getFile($statusFilePath);
			KalturaLog::info('Status file was found');
			return $statusXml;
		}
		catch(kFileTransferMgrException $ex) // file is still missing
		{
			KalturaLog::info('File doesn\'t exist yet, so no internal failure was found till now');
			return false;
		}
	}

	protected function syncPlaylists($videoId, KalturaYouTubeDistributionJobProviderData $providerData)
	{
		$fieldValues = unserialize($providerData->fieldValues);
		$youtubeChannel = isset($fieldValues[KalturaYouTubeDistributionField::VIDEO_CHANNEL]) ? $fieldValues[KalturaYouTubeDistributionField::VIDEO_CHANNEL] : null;
		$newVideoPlaylists = isset($fieldValues[KalturaYouTubeDistributionField::PLAYLISTS]) ? $fieldValues[KalturaYouTubeDistributionField::PLAYLISTS] : null;
		$clientId = $providerData->googleClientId;
		$clientSecret   = $providerData->googleClientSecret;
		$tokenData = $providerData->googleTokenData;

		if (!$newVideoPlaylists && !$tokenData)
		{
			// no playlists and token was not setup, do nothing
			return $providerData->currentPlaylists;
		}
		if (!$youtubeChannel)
		{
			KalturaLog::err('YouTube channel was not found');
			return $providerData->currentPlaylists;
		}
		if (!$videoId)
		{
			KalturaLog::err('No video id');
			return $providerData->currentPlaylists;
		}
		$youtubeService = YouTubeDistributionGoogleClientHelper::getYouTubeService($clientId, $clientSecret, $tokenData);

		$playlistSync = new YouTubeDistributionPlaylistsSync($youtubeService);

		$currentPlaylists = $playlistSync->sync($youtubeChannel, $videoId, $providerData->currentPlaylists, $newVideoPlaylists);
		return $currentPlaylists;
	}

	/**
	 * 
	 * @param KalturaYouTubeDistributionProfile $distributionProfile
	 * @return sftpMgr
	 */
	protected function getSFTPManager(KalturaYouTubeDistributionProfile $distributionProfile)
	{
		if (!is_null($this->_sftpManager))
			return $this->_sftpManager;

		$serverUrl = $distributionProfile->sftpHost;
		$loginName = $distributionProfile->sftpLogin;
		$publicKeyFile = $this->getFileLocationForSFTPKey($distributionProfile->id, $distributionProfile->sftpPublicKey, 'publickey');
		$privateKeyFile = $this->getFileLocationForSFTPKey($distributionProfile->id, $distributionProfile->sftpPrivateKey, 'privatekey');
		$port = 22;
		if ($distributionProfile->sftpPort)
			$port = $distributionProfile->sftpPort;
		$engineOptions = isset(KBatchBase::$taskConfig->engineOptions) ? KBatchBase::$taskConfig->engineOptions->toArray() : array();
		$sftpManager = kFileTransferMgr::getInstance(kFileTransferMgrType::SFTP, $engineOptions);
		$sftpManager->loginPubKey($serverUrl, $loginName, $publicKeyFile, $privateKeyFile, null, $port);
		$this->_sftpManager = $sftpManager;
		return $this->_sftpManager;
	}
	
	/*
	 * Lazy saving of the key to a temporary path, the key will exist in this location until the temp files are purged 
	 */
	protected function getFileLocationForSFTPKey($distributionProfileId, $keyContent, $fileName) 
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
	
	/*
	 * Creates and return the temp directory used for this distribution profile 
	 */
	protected function getTempDirectoryForProfile($distributionProfileId)
	{
		$metadataTempFilePath = $this->tempDirectory . '/' . self::TEMP_DIRECTORY . '/'  . $distributionProfileId . '/';
		if (!file_exists($metadataTempFilePath))
			mkdir($metadataTempFilePath, 0777, true);
		return $metadataTempFilePath;
	}

	/**
	 * Uploads the empty delivery.complete marker file
	 * @param sftpMgr $sftpManager
	 */
	public function setDeliveryComplete(sftpMgr $sftpManager, $directoryName)
	{
		$path = $directoryName.'/'.'delivery.complete';
		$sftpManager->filePutContents($path, '');
	}
}