<?php
require_once KALTURA_ROOT_PATH.'/vendor/google-api-php-client-1.1.2/src/Google/autoload.php';

/**
 * @package plugins.youtubeApiDistribution
 * @subpackage lib
 */
class YoutubeApiDistributionEngineLogger extends Google_Logger_Abstract
{
	/* (non-PHPdoc)
	 * @see Google_Logger_Abstract::write()
	 */
	protected function write($message)
	{
		KalturaLog::debug($message);
	}
}

/**
 * @package plugins.youtubeApiDistribution
 * @subpackage lib
 */
class YoutubeApiDistributionEngine extends DistributionEngine implements 
	IDistributionEngineSubmit,
	IDistributionEngineCloseSubmit, 
	IDistributionEngineUpdate,
	IDistributionEngineDelete,
	IDistributionEngineEnable,
	IDistributionEngineDisable
{
	protected $tempXmlPath;
	protected $timeout = 90;
	protected  $processedTimeout = 300;
	
	/* (non-PHPdoc)
	 * @see DistributionEngine::configure()
	 */
	const MAXIMUM_NUMBER_OF_UPLOAD_CHUNK_RETRY = 3;

	const DEFAULT_CHUNK_SIZE_BYTE = 1048576; // 1024 * 1024

	const TIME_TO_WAIT_FOR_YOUTUBE_TRANSCODING = 5;

	public function configure()
	{
		parent::configure();
		
		if(KBatchBase::$taskConfig->params->tempXmlPath)
		{
			$this->tempXmlPath = KBatchBase::$taskConfig->params->tempXmlPath;
			if(!is_dir($this->tempXmlPath))
				mkdir($this->tempXmlPath, 0777, true);
		}
		else
		{
			KalturaLog::err("params.tempXmlPath configuration not supplied");
			$this->tempXmlPath = sys_get_temp_dir();
		}
		
		if (isset(KBatchBase::$taskConfig->params->youtubeApi))
		{
			if (isset(KBatchBase::$taskConfig->params->youtubeApi->timeout))
				$this->timeout = KBatchBase::$taskConfig->params->youtubeApi->timeout;

			if (isset(KBatchBase::$taskConfig->params->youtubeApi->processedTimeout))
				$this->processedTimeout = KBatchBase::$taskConfig->params->youtubeApi->processedTimeout;
		}
		
		KalturaLog::info('Request timeout was set to ' . $this->timeout . ' seconds');
	}

	/**
	 * @param KalturaYoutubeApiDistributionJobProviderData $providerData
	 * @return Google_Client
	 */
	protected function initClient(KalturaYoutubeApiDistributionProfile $distributionProfile)
	{
		$options = array(
			CURLOPT_VERBOSE => true,
			CURLOPT_STDERR => STDOUT,
			CURLOPT_TIMEOUT => $this->timeout,
		);
		
		$client = new Google_Client();
		$client->getIo()->setOptions($options);
		$client->setLogger(new YoutubeApiDistributionEngineLogger($client));
		$client->setClientId($distributionProfile->googleClientId);
		$client->setClientSecret($distributionProfile->googleClientSecret);
		$client->setAccessToken(str_replace('\\', '', $distributionProfile->googleTokenData));
		
		return $client;
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaYoutubeApiDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaYoutubeApiDistributionProfile");
	
		return $this->doSubmit($data, $data->distributionProfile);
	}

	/**
	 * Tries to transalte the friendly name of the category to the api value, if not found the distribution progile default will be used
	 * @param Google_Service_YouTube $youtube
	 * @param KalturaYoutubeApiDistributionProfile $distributionProfile
	 * @param string $category
	 * @return int
	 */
	protected function translateCategory(Google_Service_YouTube $youtube, KalturaYoutubeApiDistributionProfile $distributionProfile, $categoryName)
	{
		if($categoryName)
		{
			$categoriesListResponse = $youtube->videoCategories->listVideoCategories('id,snippet', array('regionCode' => 'us'));
			foreach($categoriesListResponse->getItems() as $category)
			{
				if($category['snippet']['title'] == $categoryName)
					return $category['id'];
			}
			KalturaLog::warning("Partner [$distributionProfile->partnerId] Distribution-Profile [$distributionProfile->id] category [$categoryName] not found");
		}
		
		if($distributionProfile->defaultCategory)
			return $distributionProfile->defaultCategory;
		
		return $categoryName;
	}

	protected function doCloseSubmit(KalturaDistributionSubmitJobData $data, KalturaYoutubeApiDistributionProfile $distributionProfile)
	{
		$client = $this->initClient($distributionProfile);
		$youtube = new Google_Service_YouTube($client);

		$listResponse = $youtube->videos->listVideos('status', array('id' => $data->entryDistribution->remoteId));
		$video = reset($listResponse->getItems());
		KalturaLog::debug("Video: " . print_r($video, true));
		
		switch($video['modelData']['status']['uploadStatus'])
		{
			case 'deleted':
				throw new Exception("Video deleted on YouTube side");
				
			case 'failed':
				switch($video['modelData']['status']['failureReason'])
				{
					case 'codec':
						throw new Exception("Video failed because of its codec");
					case 'conversion':
						throw new Exception("Video failed on conversion");
					case 'emptyFile':
						throw new Exception("Video failed because the file is empty");
					case 'invalidFile':
						throw new Exception("Video failed - invalid file");
					case 'tooSmall':
						throw new Exception("Video failed because the file is too small");
					case 'uploadAborted':
						throw new Exception("Video failed because upload aborted");
					default:
						throw new Exception("Unknown failure reason [" . $video['modelData']['status']['failureReason'] . "]");
				}
				
			case 'rejected':
				switch($video['modelData']['status']['rejectionReason'])
				{
					case 'claim':
						throw new Exception("Video rejected due to claim");
					case 'copyright':
						throw new Exception("Video rejected due to copyrights");
					case 'duplicate':
						throw new Exception("Video rejected due to duplication");
					case 'inappropriate':
						throw new Exception("Video rejected because it's inappropriate");
					case 'length':
						throw new Exception("Video rejected due its length");
					case 'termsOfUse':
						throw new Exception("Video rejected because it crossed the terms of use");
					case 'trademark':
						throw new Exception("Video rejected due to trademark");
					case 'uploaderAccountClosed':
						throw new Exception("Video rejected because uploader account closed");
					case 'uploaderAccountSuspended':
						throw new Exception("Video rejected because uploader account suspended");
					default:
						throw new Exception("Unknown rejection reason [" . $video['modelData']['status']['rejectionReason'] . "]");
				}
				
			case 'uploaded':
				return false;
				
			case 'processed':
				return true;
				
			default:
				throw new Exception("Unknown video status [" . $video['modelData']['status']['uploadStatus'] . "]");
		}
	}
	
	protected function doSubmit(KalturaDistributionSubmitJobData $data, KalturaYoutubeApiDistributionProfile $distributionProfile)
	{
		$client = $this->initClient($distributionProfile);
		$youtube = new Google_Service_YouTube($client);

		if($data->entryDistribution->remoteId)
		{
			$data->remoteId = $data->entryDistribution->remoteId;
		}
		else
		{
			$videoPath = $data->providerData->videoAssetFilePath;
			if (!$videoPath)
				throw new KalturaException('No video asset to distribute, the job will fail');
			if (!file_exists($videoPath))
				throw new KalturaDistributionException("The file [$videoPath] was not found (probably not synced yet), the job will retry");
			
			$needDel = false;
			if (strstr($videoPath, ".") === false)
			{
				$videoPathNew = $this->tempXmlPath . "/" . uniqid() . ".dme";
	
				if (!file_exists($videoPathNew))
				{
					copy($videoPath,$videoPathNew);
					$needDel = true;
				}
				$videoPath = $videoPathNew;
			}
			
			$this->fieldValues = unserialize($data->providerData->fieldValues);
	
	//		$props['start_date'] = $this->getValueForField(KalturaYouTubeApiDistributionField::START_DATE);
	//		$props['end_date'] = $this->getValueForField(KalturaYouTubeApiDistributionField::END_DATE);
			
			$snippet = new Google_Service_YouTube_VideoSnippet();
			$snippet->setTitle(self::sanitizeFromHtmlTags($this->getValueForField(KalturaYouTubeApiDistributionField::MEDIA_TITLE)));
			$snippet->setDescription(self::sanitizeFromHtmlTags($this->getValueForField(KalturaYouTubeApiDistributionField::MEDIA_DESCRIPTION)));
			$snippet->setTags(explode(',', self::sanitizeFromHtmlTags($this->getValueForField(KalturaYouTubeApiDistributionField::MEDIA_KEYWORDS))));
			$snippet->setCategoryId($this->translateCategory($youtube, $distributionProfile, $this->getValueForField(KalturaYouTubeApiDistributionField::MEDIA_CATEGORY)));

			$status = new Google_Service_YouTube_VideoStatus();
			$status->setPrivacyStatus('private');
			$status->setEmbeddable(false);
			
			if($data->entryDistribution->sunStatus == KalturaEntryDistributionSunStatus::AFTER_SUNRISE)
			{
				$privacyStatus = $this->getPrivacyStatus($distributionProfile);
				KalturaLog::debug("Setting privacy status to [$privacyStatus]");
				$status->setPrivacyStatus($privacyStatus);
			}
			if($this->getValueForField(KalturaYouTubeApiDistributionField::ALLOW_EMBEDDING) == 'allowed')
			{
				$status->setEmbeddable(true);
			}
		
			$video = new Google_Service_YouTube_Video();
			$video->setSnippet($snippet);
			$video->setStatus($status);
			
			$client->setDefer(true);
			$request = $youtube->videos->insert("status,snippet", $video);

			$media = new Google_Http_MediaFileUpload($client, $request, 'video/*', null, true, self::DEFAULT_CHUNK_SIZE_BYTE);
			$media->setFileSize(filesize($videoPath));
			$ingestedVideo = self::uploadInChunks($media,$videoPath, self::DEFAULT_CHUNK_SIZE_BYTE);
			$client->setDefer(false);
	
			$data->remoteId = $ingestedVideo->getId();
	
			if ($needDel == true)
			{
				unlink($videoPath);
			}
		}
		
		if (!empty($data->providerData->captionsInfo))
		{
			$startCheckingReadyTime = time();
			while (!$this->isVideoReady($youtube, $data->remoteId))
			{
				sleep(self::TIME_TO_WAIT_FOR_YOUTUBE_TRANSCODING);
				if ( (time() - $startCheckingReadyTime) > $this->processedTimeout )
				{
					throw new kTemporaryException("Video transcoding on youtube has timed out");
				}
			}
			
			foreach ($data->providerData->captionsInfo as $captionInfo)
			{
				/* @var $captionInfo KalturaYouTubeApiCaptionDistributionInfo */
				if ($captionInfo->action == KalturaYouTubeApiDistributionCaptionAction::SUBMIT_ACTION)
				{
					$data->mediaFiles[] = $this->submitCaption($youtube, $captionInfo, $data->remoteId);
				}
			}
		}
		$playlistIds = explode(',', $this->getValueForField(KalturaYouTubeApiDistributionField::MEDIA_PLAYLIST_IDS));
		$this->syncPlaylistIds($youtube, $data->remoteId, $playlistIds); 
		
		return $distributionProfile->assumeSuccess;
	}

	private static function sanitizeFromHtmlTags($filed)
	{
		/* In php 7 this should be tested to make sure that it works properly since default value for html_entity_decode is not null */
		return strip_tags(html_entity_decode(preg_replace('/\<br(\s*)?\/?\>/i', "\n", $filed),null, 'UTF-8'));
	}

	protected function doUpdate(KalturaDistributionUpdateJobData $data, KalturaYoutubeApiDistributionProfile $distributionProfile, $enable = true)
	{
		$this->fieldValues = unserialize($data->providerData->fieldValues);

		$client = $this->initClient($distributionProfile);
		$youtube = new Google_Service_YouTube($client);

		$listResponse = $youtube->videos->listVideos('snippet,status', array('id' => $data->entryDistribution->remoteId));
		$video = reset($listResponse->getItems());
		
//		$props['start_date'] = $this->getValueForField(KalturaYouTubeApiDistributionField::START_DATE);
//		$props['end_date'] = $this->getValueForField(KalturaYouTubeApiDistributionField::END_DATE);
		
		$snippet = $video['snippet'];
		$snippet['title'] = self::sanitizeFromHtmlTags($this->getValueForField(KalturaYouTubeApiDistributionField::MEDIA_TITLE));
		$snippet['description'] = self::sanitizeFromHtmlTags($this->getValueForField(KalturaYouTubeApiDistributionField::MEDIA_DESCRIPTION));
		$snippet['tags'] = explode(',', self::sanitizeFromHtmlTags($this->getValueForField(KalturaYouTubeApiDistributionField::MEDIA_KEYWORDS)));
 		$snippet['category'] = $this->translateCategory($youtube, $distributionProfile, $this->getValueForField(KalturaYouTubeApiDistributionField::MEDIA_CATEGORY));
		
		$status = $video['status'];
		$status['privacyStatus'] = 'private';
		$status['embeddable'] = false;
		
		if($enable && $data->entryDistribution->sunStatus == KalturaEntryDistributionSunStatus::AFTER_SUNRISE)
		{
			$privacyStatus = $this->getPrivacyStatus($distributionProfile);
			KalturaLog::debug("Setting privacy status to [$privacyStatus]");
			$status['privacyStatus'] = $privacyStatus;
		}
		if($this->getValueForField(KalturaYouTubeApiDistributionField::ALLOW_EMBEDDING) == 'allowed')
		{
			$status['embeddable'] = true;
		}
	
		$youtube->videos->update('snippet,status', $video);

		foreach ($data->providerData->captionsInfo as $captionInfo)
		{
			/* @var $captionInfo KalturaYouTubeApiCaptionDistributionInfo */
			switch ($captionInfo->action){
				case KalturaYouTubeApiDistributionCaptionAction::SUBMIT_ACTION:
					$data->mediaFiles[] = $this->submitCaption($youtube, $captionInfo, $data->entryDistribution->remoteId);
					break;
				case KalturaYouTubeApiDistributionCaptionAction::UPDATE_ACTION:
					$this->updateCaption($youtube, $captionInfo, $data->mediaFiles);
					break;
				case KalturaYouTubeApiDistributionCaptionAction::DELETE_ACTION:
					$this->deleteCaption($youtube, $captionInfo);
					break;
			}
		}
		
		$playlistIds = explode(',', $this->getValueForField(KalturaYouTubeApiDistributionField::MEDIA_PLAYLIST_IDS));
		$this->syncPlaylistIds($youtube, $data->entryDistribution->remoteId, $playlistIds); 
		
		return true;
	}
	
	protected function doDelete(KalturaDistributionDeleteJobData $data, KalturaYoutubeApiDistributionProfile $distributionProfile)
	{
		$client = $this->initClient($distributionProfile);
		$youtube = new Google_Service_YouTube($client);
		$youtube->videos->delete($data->entryDistribution->remoteId);
		
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaYoutubeApiDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaYoutubeApiDistributionProfile");
	
		return $this->doCloseSubmit($data, $data->distributionProfile);
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineUpdate::update()
	 */
	public function update(KalturaDistributionUpdateJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaYoutubeApiDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaYoutubeApiDistributionProfile");
	
		return $this->doUpdate($data, $data->distributionProfile);
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineDisable::disable()
	 */
	public function disable(KalturaDistributionDisableJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaYoutubeApiDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaYoutubeApiDistributionProfile");
	
		return $this->doUpdate($data, $data->distributionProfile, false);
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineEnable::enable()
	 */
	public function enable(KalturaDistributionEnableJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaYoutubeApiDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaYoutubeApiDistributionProfile");
	
		return $this->doUpdate($data, $data->distributionProfile);
	}
	
	/* (non-PHPdoc)
	 * @see IDistributionEngineDelete::delete()
	 */
	public function delete(KalturaDistributionDeleteJobData $data)
	{
		if(!$data->distributionProfile || !($data->distributionProfile instanceof KalturaYoutubeApiDistributionProfile))
			throw new Exception("Distribution profile must be of type KalturaYoutubeApiDistributionProfile");
	
		return $this->doDelete($data, $data->distributionProfile);
	}
	
	protected function getValueForField($fieldName)
	{
		if (isset($this->fieldValues[$fieldName])) {
			return $this->fieldValues[$fieldName];
		}
		return null;
	}

	/**
	 * @param KalturaYoutubeApiDistributionProfile $distributionProfile
	 * @return null|string
	 */
	protected function getPrivacyStatus(KalturaYoutubeApiDistributionProfile $distributionProfile)
	{
		$privacyStatus = $this->getValueForField(KalturaYouTubeApiDistributionField::ENTRY_PRIVACY_STATUS);
		if ($privacyStatus == "" || is_null($privacyStatus))
		{
			$privacyStatus = $distributionProfile->privacyStatus;
		}
		return $privacyStatus;
	}

	private function updateRemoteMediaFileVersion(KalturaDistributionRemoteMediaFileArray &$remoteMediaFiles, KalturaYouTubeApiCaptionDistributionInfo $captionInfo){
		/* @var $mediaFile KalturaDistributionRemoteMediaFile */
		foreach ($remoteMediaFiles as $remoteMediaFile){
			if ($remoteMediaFile->assetId == $mediaFile->assetId){
				$remoteMediaFile->version = $captionInfo->version;
				break;
			}			
		}
	}
	
	protected function deleteCaption(Google_Service_YouTube $youtube, KalturaYouTubeApiCaptionDistributionInfo $captionInfo)
	{
		$youtube->captions->delete($captionInfo->remoteId);
	}
	
	protected function updateCaption(Google_Service_YouTube $youtube, KalturaYouTubeApiCaptionDistributionInfo $captionInfo, KalturaDistributionRemoteMediaFileArray &$mediaFiles)
	{
		$captionSnippet = new Google_Service_YouTube_CaptionSnippet();
		$captionSnippet->setName($captionInfo->label);
	
		$caption = new Google_Service_YouTube_Caption();
		$caption->setId($captionInfo->remoteId);
		$caption->setSnippet($captionSnippet);

		$youtube->getClient()->setDefer(true);
		$captionUpdateRequest = $youtube->captions->update('snippet', $caption);

		$media = new Google_Http_MediaFileUpload($youtube->getClient(), $captionUpdateRequest, '*/*', null, true, self::DEFAULT_CHUNK_SIZE_BYTE);
		$media->setFileSize(filesize($captionInfo->filePath));
		self::uploadInChunks($media, $captionInfo->filePath, self::DEFAULT_CHUNK_SIZE_BYTE);

		$youtube->getClient()->setDefer(false);
		
		foreach ($mediaFiles as $remoteMediaFile)
		{
			if ($mediaFiles->assetId == $captionInfo->assetId)
			{
				$mediaFiles->version = $captionInfo->version;
				break;
			}			
		}
	}
	
	protected function submitCaption(Google_Service_YouTube $youtube, KalturaYouTubeApiCaptionDistributionInfo $captionInfo, $remoteId)
	{
		if (!file_exists($captionInfo->filePath ))
			throw new KalturaDistributionException("The caption file [$captionInfo->filePath] was not found (probably not synced yet), the job will retry");
			
		$captionSnippet = new Google_Service_YouTube_CaptionSnippet();
		$captionSnippet->setVideoId($remoteId);
		$captionSnippet->setLanguage($captionInfo->language);
		$captionSnippet->setName($captionInfo->label);
	
		$caption = new Google_Service_YouTube_Caption();
		$caption->setSnippet($captionSnippet);

		$youtube->getClient()->setDefer(true);
		$insertRequest = $youtube->captions->insert('snippet', $caption);
	
		$media = new Google_Http_MediaFileUpload($youtube->getClient(), $insertRequest, '*/*', null, true, self::DEFAULT_CHUNK_SIZE_BYTE);
		$media->setFileSize(filesize($captionInfo->filePath));
		$ingestedCaption = self::uploadInChunks($media, $captionInfo->filePath, self::DEFAULT_CHUNK_SIZE_BYTE);

		$youtube->getClient()->setDefer(false);
		
		$remoteMediaFile = new KalturaDistributionRemoteMediaFile ();
		$remoteMediaFile->remoteId = $ingestedCaption['id'];
		$remoteMediaFile->version = $captionInfo->version;
		$remoteMediaFile->assetId = $captionInfo->assetId;
		return $remoteMediaFile;
	}
	
	protected function syncPlaylistIds(Google_Service_YouTube $youtube, $remoteId, array $playlistIds)
	{
		$playlistsResponseList = $youtube->playlists->listPlaylists('id,snippet', array('mine' => true));
		foreach($playlistsResponseList->getItems() as $playlist)
		{
			$playlistId = $playlist['id'];
			if(!in_array($playlistId, $playlistIds))
			{
				$playlistsItemsListResponse = $youtube->playlistItems->listPlaylistItems('snippet', array(
					'playlistId' => $playlistId,
					'videoId' => $remoteId
				));
				foreach($playlistsItemsListResponse->getItems() as $playlistItem)
				{
					$youtube->playlistItems->delete($playlistItem['id']);
				}
			}
		}
		
		foreach($playlistIds as $playlistId)
		{
			if(!$playlistId)
				continue;
				
			$playlistsItemsListResponse = $youtube->playlistItems->listPlaylistItems('snippet', array(
				'playlistId' => $playlistId,
				'videoId' => $remoteId
			));
			
			if(count($playlistsItemsListResponse->getItems()))
				continue;
				
			$resourceId = new Google_Service_YouTube_ResourceId();
			$resourceId->setKind('youtube#video');
			$resourceId->setVideoId($remoteId);
			
			$snippet = new Google_Service_YouTube_PlaylistItemSnippet();
			$snippet->setPlaylistId($playlistId);
			$snippet->setResourceId($resourceId);
			
			$playlistItem = new Google_Service_YouTube_PlaylistItem();
			$playlistItem->setSnippet($snippet);
			$youtube->playlistItems->insert('snippet', $playlistItem);
		}
	}

	protected function isVideoReady($youtube, $remoteId)
	{
		$listResponse = $youtube->videos->listVideos("status",	array('id' => $remoteId));
		if (empty($listResponse)) 
			throw new Exception("Video with remotedId ".$remoteId." not found at google");
		else 
		{
			// Since the request specified a video ID, the response only contains one video resource.
			$video = $listResponse[0];
			$videoStatus = $video['status'];
			switch($videoStatus['uploadStatus'])
			{
				case "processed":
					return true;

				case "rejected":
					if ($videoStatus['rejectionReason'] == 'duplicate')
						return true;
					else
						throw new Exception("Video was rejected by youtube, reason [" . $videoStatus['rejectionReason'] . "]");

				case "failed":
					throw new Exception("Video has failed on youtube, reason [".$videoStatus['failureReason']."]");

				default: 
					return false;
			}
		}
	}

	/**
	 * @param Google_Http_MediaFileUpload $media
	 * @throw kTemporaryException
	 * @return Google_Service_YouTube_Video
	 */
	private static function uploadInChunks($media, $filePath , $chunkSizeBytes = 1048576)
	{
		$ingestedVideo = false;
		$handle = fopen($filePath, "rb");
		while (!$ingestedVideo && !feof($handle))
		{
			$chunk = fread($handle, $chunkSizeBytes);
			$numOfTries = 0;
			while (true)
			{
				try
				{
					$ingestedVideo = $media->nextChunk($chunk);
					break;
				} catch (Google_IO_Exception $e)
				{
					KalturaLog::info("Uploading chunk to youtube failed with the message '".$e->getMessage()."' number of retries ".$numOfTries);
					$numOfTries++;
					if ($numOfTries >= self::MAXIMUM_NUMBER_OF_UPLOAD_CHUNK_RETRY)
					{
						throw new kTemporaryException($e->getMessage(), $e->getCode());
					}
				}
			}
		}
		/* @var $ingestedVideo Google_Service_YouTube_Video */
		fclose($handle);
		return $ingestedVideo;
	}
}
