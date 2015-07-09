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
	
	/* (non-PHPdoc)
	 * @see DistributionEngine::configure()
	 */
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
			$snippet->setTitle($this->getValueForField(KalturaYouTubeApiDistributionField::MEDIA_TITLE));
			$snippet->setDescription($this->getValueForField(KalturaYouTubeApiDistributionField::MEDIA_DESCRIPTION));		
			$snippet->setTags(explode(',', $this->getValueForField(KalturaYouTubeApiDistributionField::MEDIA_KEYWORDS)));
			$snippet->setCategoryId($this->translateCategory($youtube, $distributionProfile, $this->getValueForField(KalturaYouTubeApiDistributionField::MEDIA_CATEGORY)));
	
			$status = new Google_Service_YouTube_VideoStatus();
			$status->setPrivacyStatus('private');
			$status->setEmbeddable(false);
			
			if($data->entryDistribution->sunStatus == KalturaEntryDistributionSunStatus::AFTER_SUNRISE)
			{
				$status->setPrivacyStatus('public');
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
			
			$chunkSizeBytes = 1 * 1024 * 1024;
			$media = new Google_Http_MediaFileUpload($client, $request, 'video/*', null, true, $chunkSizeBytes);
			$media->setFileSize(filesize($videoPath));
	
			$ingestedVideo = false;
			$handle = fopen($videoPath, "rb");
			while (!$ingestedVideo && !feof($handle)) 
			{
				$chunk = fread($handle, $chunkSizeBytes);
				$ingestedVideo = $media->nextChunk($chunk);
			}
			/* @var $ingestedVideo Google_Service_YouTube_Video */
	
			fclose($handle);
			$client->setDefer(false);
	
			$data->remoteId = $ingestedVideo->getId();
	
			if ($needDel == true)
			{
				unlink($videoPath);
			}
		}
		
		foreach ($data->providerData->captionsInfo as $captionInfo){
			/* @var $captionInfo KalturaYouTubeApiCaptionDistributionInfo */
			if ($captionInfo->action == KalturaYouTubeApiDistributionCaptionAction::SUBMIT_ACTION)
			{
				$data->mediaFiles[] = $this->submitCaption($youtube, $captionInfo, $data->remoteId);
			}
		}
		
		$playlistIds = explode(',', $this->getValueForField(KalturaYouTubeApiDistributionField::MEDIA_PLAYLIST_IDS));
		$this->syncPlaylistIds($youtube, $data->remoteId, $playlistIds); 
		
		return $distributionProfile->assumeSuccess;
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
		$snippet['title'] = $this->getValueForField(KalturaYouTubeApiDistributionField::MEDIA_TITLE);
		$snippet['description'] = $this->getValueForField(KalturaYouTubeApiDistributionField::MEDIA_DESCRIPTION);
		$snippet['tags'] = explode(',', $this->getValueForField(KalturaYouTubeApiDistributionField::MEDIA_KEYWORDS));
 		$snippet['category'] = $this->translateCategory($youtube, $distributionProfile, $this->getValueForField(KalturaYouTubeApiDistributionField::MEDIA_CATEGORY));
		
		$status = $video['status'];
		$status['privacyStatus'] = 'private';
		$status['embeddable'] = false;
		
		if($enable && $data->entryDistribution->sunStatus == KalturaEntryDistributionSunStatus::AFTER_SUNRISE)
		{
			$status['privacyStatus'] = 'public';
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
		
		$chunkSizeBytes = 1 * 1024 * 1024;
		$youtube->getClient()->setDefer(true);
		$captionUpdateRequest = $youtube->captions->update('snippet', $caption);

		$media = new Google_Http_MediaFileUpload($youtube->getClient(), $captionUpdateRequest, '*/*', null, true, $chunkSizeBytes);
		$media->setFileSize(filesize($captionInfo->filePath));

		$updatedCaption = false;
		$handle = fopen($captionInfo->filePath, "rb");
		while (!$updatedCaption && !feof($handle))
		{
			$chunk = fread($handle, $chunkSizeBytes);
			$updatedCaption = $media->nextChunk($chunk);
		}
		fclose($handle);

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
			
		KalturaLog::debug("Submitting caption [$captionInfo->assetId]");
		
		$captionSnippet = new Google_Service_YouTube_CaptionSnippet();
		$captionSnippet->setVideoId($remoteId);
		$captionSnippet->setLanguage($captionInfo->language);
		$captionSnippet->setName($captionInfo->label);
	
		$caption = new Google_Service_YouTube_Caption();
		$caption->setSnippet($captionSnippet);
	
		$chunkSizeBytes = 1 * 1024 * 1024;
		$youtube->getClient()->setDefer(true);
		$insertRequest = $youtube->captions->insert('snippet', $caption);
	
		$media = new Google_Http_MediaFileUpload($youtube->getClient(), $insertRequest, '*/*', null, true, $chunkSizeBytes);
		$media->setFileSize(filesize($captionInfo->filePath));
	
		$ingestedCaption = false;
		$handle = fopen($captionInfo->filePath, "rb");
		while (!$ingestedCaption && !feof($handle)) 
		{
			$chunk = fread($handle, $chunkSizeBytes);
			$ingestedCaption = $media->nextChunk($chunk);
		}
	
		fclose($handle);
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
}