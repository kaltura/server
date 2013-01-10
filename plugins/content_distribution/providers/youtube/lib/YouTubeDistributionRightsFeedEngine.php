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

			return false;
		}
			
		$statusParser = new YouTubeDistributionLegacyStatusParser($statusXml);
		$status = $statusParser->getStatusForAction('Submit reference');

		if ($status != 'Success')
			throw new Exception('Distribution failed with status ['.$status.']');
			
		$referenceId = $statusParser->getReferenceId();
		$assetId = $statusParser->getAssetId();
		$videoId = $statusParser->getVideoId();

		$remoteIdHandler = new YouTubeDistributionRemoteIdHandler();
		$remoteIdHandler->setVideoId($videoId);
		$remoteIdHandler->setAssetId($assetId);
		$remoteIdHandler->setReferenceId($referenceId);
		$data->remoteId = $remoteIdHandler->getSerialized();
			
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
			
		$statusParser = new YouTubeDistributionLegacyStatusParser($statusXml);
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
			
		$statusParser = new YouTubeDistributionLegacyStatusParser($statusXml);
		$status = $statusParser->getStatusForAction('Process asset');
		if (is_null($status))
			throw new Exception('Status could not be found after distribution update');
		
		if ($status != 'Success')
			throw new Exception('Update failed with status ['.$status.']');
			
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
		$entry = $this->getEntry($data->entryDistribution->partnerId, $entryId);

		$videoFilePath = $providerData->videoAssetFilePath;
		if (!$videoFilePath)
			throw new KalturaDistributionException('No video asset to distribute, the job will fail');

		if (!file_exists($videoFilePath))
			throw new KalturaDistributionException('The file ['.$videoFilePath.'] was not found (probably not synced yet), the job will retry');

		$thumbnailFilePath = $providerData->thumbAssetFilePath;

		$videoTag = $entryId.'-video';
		$thumbnailTag = $entryId.'-thumbnail';

		$feed = new YouTubeDistributionRightsFeedHelper($distributionProfile);
		$fieldValues = unserialize($providerData->fieldValues);
		$feed->setNotificationEmail($fieldValues);
		$feed->setMetadataByFieldValues($fieldValues);
		$feed->setByXpath('video/@tag', $videoTag);
		$feed->setByXpath('asset/@tag', $videoTag);

		// video file
		$urgentReference = $fieldValues[KalturaYouTubeDistributionField::URGENT_REFERENCE_FILE];
		$feed->appendFileElement('video', $urgentReference, pathinfo($videoFilePath, PATHINFO_BASENAME), $videoTag);

		// thumbnail file
		if (file_exists($thumbnailFilePath))
		{
			$feed->appendFileElement('image', false, pathinfo($thumbnailFilePath, PATHINFO_BASENAME), $thumbnailTag);
			$feed->appendVideoArtworkElement('custom_thumbnail', $thumbnailTag);
		}

		$feed->appendVideoAssetFileRelationship($videoTag);
		$feed->setAdParamsByFieldValues($fieldValues, $videoTag, $distributionProfile->enableAdServer);
		$feed->appendRightsAdminByFieldValues($fieldValues, $videoTag);

		$sftpManager = $this->getSFTPManager($distributionProfile);
		$feed->sendFeed($sftpManager);
		$data->sentData = $feed->getXml();
		$data->results = 'none'; // otherwise kContentDistributionFlowManager won't save sentData

		// upload the video
		$videoSFTPPath = $feed->getDirectoryName() . '/' . pathinfo($videoFilePath, PATHINFO_BASENAME);
		$sftpManager->putFile($videoSFTPPath, $videoFilePath);

		// upload the thumbnail if exists
		if (file_exists($thumbnailFilePath))
		{
			$thumbnailSFTPPath = $feed->getDirectoryName() . '/' . pathinfo($thumbnailFilePath, PATHINFO_BASENAME);
			$sftpManager->putFile($thumbnailSFTPPath, $thumbnailFilePath);
		}

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
		$remoteIdHandler = YouTubeDistributionRemoteIdHandler::initialize($data->remoteId);
		$feed = new YouTubeDistributionRightsFeedHelper($distributionProfile);
		$feed->setByXpath('video/@action', 'delete');
		$feed->setByXpath('video/@action', $remoteIdHandler->getVideoId());

		$sftpManager = $this->getSFTPManager($distributionProfile);
		
		$feed->sendFeed($sftpManager);
		$data->sentData = $feed->getXml();
		$data->results = 'none'; // otherwise kContentDistributionFlowManager won't save sentData
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
		$entry = $this->getEntry($data->entryDistribution->partnerId, $entryId);

		$remoteIdHandler = YouTubeDistributionRemoteIdHandler::initialize($data->remoteId);

		$feed = new YouTubeDistributionRightsFeedHelper($distributionProfile);
		$fieldValues = unserialize($providerData->fieldValues);
		if ($remoteIdHandler->getVideoId())
			$feed->setVideoMetadataByFieldValues($fieldValues);
		if ($remoteIdHandler->getAssetId())
			$feed->setAssetMetadataByFieldValues($fieldValues);

		$sftpManager = $this->getSFTPManager($distributionProfile);

		// thumbnail file
		$thumbnailFilePath = $providerData->thumbAssetFilePath;
		if (file_exists($thumbnailFilePath))
		{
			$feed->appendFileElement('image', false, pathinfo($thumbnailFilePath, PATHINFO_BASENAME), $thumbnailTag);
			$feed->appendVideoArtworkElement('custom_thumbnail', $thumbnailTag);
			$thumbnailSFTPPath = $feed->getDirectoryName() . '/' . pathinfo($thumbnailFilePath, PATHINFO_BASENAME);
			$sftpManager->putFile($thumbnailSFTPPath, $thumbnailFilePath);
		}

		$feed->sendFeed($sftpManager);
		$data->sentData = $feed->getXml();
		$data->results = 'none'; // otherwise kContentDistributionFlowManager won't save sentData
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
		$engineOptions = isset($this->taskConfig->engineOptions) ? $this->taskConfig->engineOptions->toArray() : array();
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
		$metadataTempFilePath = kConf::get('temp_folder') . '/' . self::TEMP_DIRECTORY . '/'  . $distributionProfileId . '/';
		if (!file_exists($metadataTempFilePath))
			mkdir($metadataTempFilePath, 0777, true);
		return $metadataTempFilePath;
	}
}