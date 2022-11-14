<?php
/**
 * @package plugins.WebexAPIDropFolder
 */
class KWebexAPIDropFolderEngine extends KDropFolderFileTransferEngine
{
	/**
	 * @var kWebexAPIClient
	 */
	protected $webexClient;

	
	protected function initWebexClient(KalturaDropFolder $dropFolder)
	{
		$refreshToken = isset($dropFolder->refreshToken) ? $dropFolder->refreshToken : null;
		$accessToken = isset($dropFolder->accessToken) ? $dropFolder->accessToken : null;
		$clientId = isset($dropFolder->clientId) ? $dropFolder->clientId : null;
		$clientSecret = isset($dropFolder->clientSecret) ? $dropFolder->clientSecret : null;
		$accessExpiresIn = isset($dropFolder->accessExpiresIn) ? $dropFolder->accessExpiresIn : null;
		return new kWebexAPIClient($dropFolder->baseURL, $refreshToken, $clientId, $clientSecret, $accessToken, $accessExpiresIn);
	}
	
	public function watchFolder(KalturaDropFolder $dropFolder)
	{
		$this->webexClient = $this->initWebexClient($dropFolder);
		$this->dropFolder = $dropFolder;
		KalturaLog::info('Watching folder [' . $this->dropFolder->id . ']');
		
		$response = $this->webexClient->getRecordingsList();
		$recordingsList = json_decode($response, true);
		KalturaLog::info('Response from Webex recordings: ' . print_r($recordingsList));
		$items = $recordingsList['items'];
		if (!$items)
		{
			KaltureLog::debug('No items in response');
			return;
		}
		
		foreach ($items as $item)
		{
			KalturaLog::info($item['meetingId']);
			KalturaLog::info($item['createTime']);
			KalturaLog::info($item['topic']);
			KalturaLog::info($item['format']);
			KalturaLog::info($item['serviceType']);
			
			$response = $this->webexClient->getRecording($item['id']);
			$recordingInfo = json_decode($response, true);
			KalturaLog::info('Response from Webex recordings: ' . print_r($recordingInfo));
			KalturaLog::info(print_r($recordingInfo));
			
			$recordingFileName = $recordingInfo['topic'];
			$dropFolderFilesMap = $this->loadDropFolderFiles($recordingFileName);
			if (count($dropFolderFilesMap) === 0)
			{
				KalturaLog::info("Creating Drop Folder File for file: $recordingFileName");
				$this->addDropFolderFile($recordingInfo);
			}
			else
			{
				KalturaLog::info("File already exists for: $recordingFileName");
			}
		}
		
		
		
		self::updateDropFolderLastMeetingHandled(time());
		
		//$this->handleExistingDropFolderFiles();
	}
	
	protected function addDropFolderFile($recordingInfo)
	{
		try
		{
			$webexDropFolderFile = $this->allocateWebexDropFolderFile($recordingInfo);
			
			KalturaLog::debug("Adding new WebexDropFolderFile: " . print_r($webexDropFolderFile, true));
			$dropFolderFile = $this->dropFolderFileService->add($webexDropFolderFile);
			return $dropFolderFile;
		}
		catch (Exception $e)
		{
			KalturaLog::err('Cannot add new drop folder file with name ['. $recordingInfo['topic'] .'] - '.$e->getMessage());
			return null;
		}
	}
	
	protected function allocateWebexDropFolderFile($recordingInfo)
	{
		$webexDropFolderFile = new KalturaWebexAPIDropFolderFile();
		$webexDropFolderFile->dropFolderId = $this->dropFolder->id;
		$webexDropFolderFile->fileName = $recordingInfo['topic'];
		$webexDropFolderFile->fileSize = $recordingInfo['sizeBytes'];
		$webexDropFolderFile->contentUrl = $recordingInfo['temporaryDirectDownloadLinks']['recordingDownloadLink'];
		return $webexDropFolderFile;
	}
	
	protected function updateDropFolderLastMeetingHandled($lastHandledMeetingTime)
	{
		$updateDropFolder = new KalturaWebexAPIDropFolder();
		$updateDropFolder->lastHandledMeetingTime = $lastHandledMeetingTime;
		$this->dropFolderPlugin->dropFolder->update($this->dropFolder->id, $updateDropFolder);
		KalturaLog::debug("Last handled meetings time is: $lastHandledMeetingTime");
	}

	protected function handleExistingDropFolderFile(KalturaDropFolderFile $dropFolderFile)
	{
	}
	
	protected function purgeFile(KalturaDropFolderFile $dropFolderFile)
	{
	}

	public function processFolder(KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data)
	{
		KalturaLog::debug('Start processing Webex Folder');
		KBatchBase::impersonate($job->partnerId);
		if (!$data->contentMatchPolicy == KalturaDropFolderContentFileHandlerMatchPolicy::ADD_AS_NEW)
		{
			throw new kApplicativeException(KalturaDropFolderErrorCode::CONTENT_MATCH_POLICY_UNDEFINED, 'No content match policy is defined for drop folder');
		}
		
		/* @var KalturaWebexAPIDropFolderFile $dropFolderFile*/
		$dropFolderFileId = $data->dropFolderFileIds;
		$dropFolderFile = $this->dropFolderFileService->get($dropFolderFileId);
		
		/* @var KalturaWebexAPIDropFolder $dropFolder */
		$dropFolder = $this->dropFolderPlugin->dropFolder->get($data->dropFolderId);
		if (!$dropFolder->webexAPIVendorIntegration)
		{
			throw new kExternalException(KalturaDropFolderErrorCode::MISSING_CONFIG, DropFolderPlugin::MISSING_CONFIG_MESSAGE);
		}
		
		$webexBaseURL = $dropFolder->baseURL;
		//$zoomRecordingProcessor = new zoomMeetingProcessor($webexBaseURL, $dropFolder);
		$entry = $this->createEntryFromRecording($dropFolderFile, $job->partnerId);
		//$this->setEntryCategory($entry, $recording->meetingMetadata->meetingId);
		//$this->handleParticipants($updatedEntry, $validatedUsers);
		//$entry = KBatchBase::$kClient->baseEntry->update($entry->id, $updatedEntry);
		
		$kFlavorAsset = new KalturaFlavorAsset();
		//$kFlavorAsset->tags = self::TAG_SOURCE;
		//$kFlavorAsset->flavorParamsId = self::SOURCE_FLAVOR_ID;
		$kFlavorAsset->fileExt = strtolower($dropFolderFile->recordingFile->fileExtension);
		$flavorAsset = KBatchBase::$kClient->flavorAsset->add($entry->getId(), $kFlavorAsset);
		
		$resource = new KalturaUrlResource();
		$resource->url = $dropFolderFile->contentUrl;
		$resource->forceAsyncDownload = true;
		
		$assetParamsResourceContainer =  new KalturaAssetParamsResourceContainer();
		$assetParamsResourceContainer->resource = $resource;
		$assetParamsResourceContainer->assetParamsId = $flavorAsset->flavorParamsId;
		KBatchBase::$kClient->media->updateContent($entry->getId(), $resource);
		
		$this->updateDropFolderFile($entry->getId() , $dropFolderFile);
		
		KBatchBase::unimpersonate();
	}
	
	/**
	 * @param kalturaZoomDropFolderFile $dropFolerFile
	 * @param string $ownerId
	 * @return entry
	 * @throws Exception
	 */
	protected function createEntryFromRecording($dropFolerFile, $ownerId)
	{
		$newEntry = new KalturaMediaEntry();
		$newEntry->sourceType = KalturaSourceType::URL;
		if ($dropFolerFile->recordingFile->fileType == KalturaRecordingFileType::AUDIO)
		{
			$newEntry->mediaType = KalturaMediaType::AUDIO;
		}
		else
		{
			$newEntry->mediaType = KalturaMediaType::VIDEO;
		}
		$newEntry->description = $this->createEntryDescriptionFromRecording($dropFolerFile);
		$newEntry->name = $dropFolerFile->meetingMetadata->topic;
		$newEntry->userId = $ownerId;
		$newEntry->conversionProfileId = $this->dropFolder->conversionProfileId;
		//$newEntry->adminTags = self::ADMIN_TAG_ZOOM;
		//$newEntry->referenceId = self::ZOOM_PREFIX . $dropFolerFile->meetingMetadata->uuid;
		//KBatchBase::impersonate($this->dropFolder->partnerId);
		$kalturaEntry = KBatchBase::$kClient->baseEntry->add($newEntry);
		//KBatchBase::unimpersonate();
		return $kalturaEntry;
	}
	
	/**
	 * @param KalturaZoomDropFolderFile $recording
	 * @return string
	 */
	protected function createEntryDescriptionFromRecording($recording)
	{
		//$meetingStartTime = gmdate("Y-m-d h:i:sa", $recording->meetingMetadata->meetingStartTime);
		//return "Webex Recording ID: {$recording->meetingMetadata->meetingId}\nUUID: {$recording->meetingMetadata->uuid}\nMeeting Time: {$meetingStartTime}";
	}
	
	function updateDropFolderFile($entryId , $dropFolderFile)
	{
		$kWebexDropFolderFile = new KalturaWebexAPIDropFolderFile();
		$kWebexDropFolderFile->entryId = $entryId;
		$this->dropFolderFileService->update($dropFolderFile->id, $kWebexDropFolderFile);
		$this->dropFolderFileService->updateStatus($dropFolderFile->id, KalturaDropFolderFileStatus::HANDLED);
	}
}
