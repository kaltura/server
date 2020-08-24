<?php
/**
 * @package plugins.youTubeDistribution
 * @subpackage lib
 */
class YouTubeDistributionCsvEngine extends YouTubeDistributionRightsFeedEngine
{
	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaYouTubeDistributionProfile $distributionProfile
	 * @param KalturaYouTubeDistributionJobProviderData $providerData
	 */
	protected function handleSubmit(KalturaDistributionJobData $data, KalturaYouTubeDistributionProfile $distributionProfile, KalturaYouTubeDistributionJobProviderData $providerData)
	{
		$videoFilePath = $providerData->videoAssetFilePath;
		$thumbAssetId = $providerData->thumbAssetId;
		$thumbAssetFilePath = $providerData->thumbAssetFilePath;

		if (!$videoFilePath)
			throw new KalturaDistributionException('No video asset to distribute, the job will fail');

		if (!file_exists($videoFilePath))
			throw new KalturaDistributionException('The file ['.$videoFilePath.'] was not found (probably not synced yet), the job will retry');

		$csvMap = unserialize($providerData->submitCsvMap);
		$videoCsv = implode(',' ,array_keys($csvMap )) .'\n';
		$videoCsv .= implode(',' ,array_values($csvMap)) .'\n';

		$sftpManager = $this->getSFTPManager($distributionProfile);
		// create CSV file
		$fp = tempnam(sys_get_temp_dir(), 'temp.').".csv";
		$file = fopen($fp, 'w');
		fputcsv($file, array_keys($csvMap));
		fputcsv($file, array_values($csvMap));
		fclose($file);

		$sftpManager->putFile($providerData->sftpDirectory.'/'.$providerData->sftpMetadataFilename, $fp);
		unlink($fp);

		$data->sentData = $videoCsv;
		$data->results = 'none'; // otherwise kContentDistributionFlowManager won't save sentData

		// upload the video
		$videoSFTPPath = $providerData->sftpDirectory.'/'.pathinfo($videoFilePath, PATHINFO_BASENAME);
		$sftpManager->putFile($videoSFTPPath, $videoFilePath);

		// upload the thumbnail if exists
		$this->handleThumbUpload($thumbAssetId, $providerData, $sftpManager, $thumbAssetFilePath);

		$this->setDeliveryComplete($sftpManager, $providerData->sftpDirectory);
	}

	public function handleThumbUpload($thumbAssetId, $providerData, $sftpManager, $thumbnailFilePath = null)
	{
		$thumbAssetPath = $this->getAssetFile($thumbAssetId, sys_get_temp_dir(), pathinfo($thumbnailFilePath, PATHINFO_BASENAME));

		if ($thumbAssetPath && file_exists($thumbAssetPath))
		{
			try
			{
				$thumbnailSFTPPath = $providerData->sftpDirectory . '/' . pathinfo($thumbAssetPath, PATHINFO_BASENAME);
				$sftpManager->putFile($thumbnailSFTPPath, $thumbAssetPath);
			}
			catch(Exception $e)
			{
				KalturaLog::err($e);
			}
			unlink($thumbAssetPath);
		}
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		$reportCsv = $this->fetchFile($data, $data->distributionProfile, $data->providerData, 'report');

		if ($reportCsv  === false) // no status yet
		{
			// try to get batch status xml to see if there is an internal error on youtube's batch
			$batchStatus = $this->fetchBatchStatus($data, $data->distributionProfile, $data->providerData);
			if ($batchStatus)
				throw new Exception('Internal failure on YouTube, internal_failure-status.xml was found. Error ['.$batchStatus.']');

			return false; // return false to recheck again on next job closing iteration
		}
			
		$statusParser = new YouTubeDistributionCsvParser($reportCsv);
		$status = $statusParser->getStatusForAction('Status');

		if ($status != 'Successful')
		{
			$errorsCsv = $this->fetchFile($data, $data->distributionProfile, $data->providerData, 'errors');
			$errorsParser = new YouTubeDistributionCsvParser($errorsCsv);
			$errors = $errorsParser->getErrorsSummary();
			throw new Exception('Distribution failed with status ['.$status.'] and errors ['.implode(',', $errors).']');
		}
			
		$referenceId = $statusParser->getReferenceId();
		$assetId = $statusParser->getAssetId();
		$videoId = $statusParser->getVideoId();

		$sftpManager = $this->getSFTPManager($data->distributionProfile);
		$captionCsvMap = unserialize($data->providerData->captionsCsvMap);
		$this->addCaptions($data->providerData, $sftpManager, $data);

		if ($videoId && !empty($captionCsvMap))
		{
			$fp = tempnam(sys_get_temp_dir(), 'temp.') . ".csv";
			$file = fopen($fp, 'w');
			fputcsv($file, array('video_id','language','caption_file'));
			foreach ($captionCsvMap as $captionItem )
			{
				$row = array( $videoId , $captionItem['language'] , $captionItem['caption_file']);
				fputcsv($file, $row);
			}
			// create CSV file
			fclose($file);

			$sftpManager->putFile($data->providerData->sftpDirectory.'/'.$data->providerData->sftpMetadataFilename, $fp);
			$this->setDeliveryComplete($sftpManager,  $data->providerData->sftpDirectory);
			unlink($fp);
		}

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

	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaYouTubeDistributionProfile $distributionProfile
	 * @param KalturaYouTubeDistributionJobProviderData $providerData
	 */
	protected function handleUpdate(KalturaDistributionJobData $data, KalturaYouTubeDistributionProfile $distributionProfile, KalturaYouTubeDistributionJobProviderData $providerData)
	{
		$thumbAssetFilePath = $providerData->thumbAssetFilePath;
		$thumbAssetId = $providerData->thumbAssetId;

		$sftpManager = $this->getSFTPManager($distributionProfile);
		$updateCsvMap = unserialize($providerData->updateCsvMap);
		$videoCsv = implode(',' ,array_keys($updateCsvMap)) .'\n';
		$videoCsv .= implode(',' ,array_values($updateCsvMap)) .'\n';

		//create update Csv
		$fp = tempnam(sys_get_temp_dir(), 'temp.').".csv";
		$file = fopen($fp, 'w');
		fputcsv($file, array_keys($updateCsvMap));
		fputcsv($file, array_values($updateCsvMap));
		fclose($file);

		$sftpManager->putFile($providerData->sftpDirectory.'/'.$providerData->sftpMetadataFilename, $fp);
		unlink($fp);

		$data->sentData = $videoCsv;
		$data->results = 'none'; // otherwise kContentDistributionFlowManager won't save sentData

		// upload the thumbnail if exists
		$this->handleThumbUpload($thumbAssetId, $providerData, $sftpManager, $thumbAssetFilePath);

		$this->setDeliveryComplete($sftpManager, $providerData->sftpDirectory);
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseUpdate::closeUpdate()
	 */
	public function closeUpdate(KalturaDistributionUpdateJobData $data)
	{
		$statusXml = $this->fetchFile($data, $data->distributionProfile, $data->providerData, "status", "xml");

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

		//update captions
		$captionCsvMap = unserialize($data->providerData->captionsCsvMap);
		if ($videoId && !empty($captionCsvMap))
		{
			$sftpManager = $this->getSFTPManager($data->distributionProfile);
			$fp = tempnam(sys_get_temp_dir(), 'temp.') . ".csv";
			$file = fopen($fp, 'w');
			fputcsv($file, array('video_id','language','caption_file'));
			foreach ($captionCsvMap as $captionItem)
			{
				$row = array( $videoId , $captionItem['language'] , $captionItem['caption_file']);
				fputcsv($file, $row);
			}
			fclose($file);
			$sftpManager->putFile($data->providerData->sftpDirectory . '/' . $data->providerData->sftpMetadataFilename, $fp);
			$this->setDeliveryComplete($sftpManager, $data->providerData->sftpDirectory);
			unlink($fp);
		}

		$providerData = $data->providerData;
		$newPlaylists = $this->syncPlaylists($videoId, $providerData);
		if ($newPlaylists)
			$providerData->currentPlaylists = implode(',', $newPlaylists);

		return true;
	}

	/**
	 * Deleting a video is done via api call
	 * @param KalturaDistributionJobData $data
	 * @param KalturaYouTubeDistributionProfile $distributionProfile
	 * @param KalturaYouTubeDistributionJobProviderData $providerData
	 */
	protected function handleDelete(KalturaDistributionJobData $data, KalturaYouTubeDistributionProfile $distributionProfile, KalturaYouTubeDistributionJobProviderData $providerData)
	{
		$videoIdsToDelete = unserialize($providerData->deleteVideoIds);

		if (empty($videoIdsToDelete))
			return;

		$clientId = $providerData->googleClientId;
		$clientSecret   = $providerData->googleClientSecret;
		$tokenData = $providerData->googleTokenData;

		if (!$tokenData)// no token was not setup, do nothing
			throw new KalturaDistributionException('No google Token was set. the job will fail');

		$youtubeService = YouTubeDistributionGoogleClientHelper::getYouTubeService($clientId, $clientSecret, $tokenData);
		foreach($videoIdsToDelete as $videoIdToDelete)
		{
			KalturaLog::debug("Deleting video with id $videoIdToDelete ");
			$res = $youtubeService->videos->delete($videoIdToDelete);
			KalturaLog::debug("Result for Deleting $videoIdToDelete: " .print_r($res,true));
		}

		$data->sentData = implode(',',$videoIdsToDelete);
		$data->results = 'none'; // otherwise kContentDistributionFlowManager won't save sentData
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseDelete::closeDelete()
	 */
	public function closeDelete(KalturaDistributionDeleteJobData $data)
	{
		return true;
	}

	/**
	 * @param KalturaDistributionJobData $data
	 * @param KalturaYouTubeDistributionProfile $distributionProfile
	 * @param KalturaYouTubeDistributionJobProviderData $providerData
	 * @return Status CSV or FALSE when status is not available yet
	 */
	protected function fetchFile(KalturaDistributionJobData $data, KalturaYouTubeDistributionProfile $distributionProfile, KalturaYouTubeDistributionJobProviderData $providerData, $prefix = '', $extension = null )
	{
		if ($extension)
			$statusFilePath = $providerData->sftpDirectory . '/' . $prefix  . '-' . $providerData->sftpMetadataFilename . "." . $extension;
		else
			$statusFilePath = $providerData->sftpDirectory . '/' . $prefix . '-' . $providerData->sftpMetadataFilename;

		$sftpManager = $this->getSFTPManager($distributionProfile);
		$statusFile = null;
		try
		{
			KalturaLog::info('Trying to get the following status file: ['.$statusFilePath.']');
			$statusFile = $sftpManager->getFile($statusFilePath);
		}
		catch(kFileTransferMgrException $ex) // file is still missing
		{
			KalturaLog::info('File doesn\'t exist yet, retry later');
			return false;
		}

		$data->results = $statusFile;
		return $statusFile;
	}
}