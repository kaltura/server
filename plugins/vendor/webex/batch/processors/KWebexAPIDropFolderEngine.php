<?php
/**
 * @package plugins.WebexAPIDropFolder
 */
class KWebexAPIDropFolderEngine extends KDropFolderFileTransferEngine
{
	const DEFAULT_WEBEX_QUERY_TIME_RANGE = kTimeConversion::MONTH;
	
	/**
	 * @var kWebexAPIClient
	 */
	protected $webexClient;
	
	/**
	 * @var int
	 */
	protected $lastFileTimestamp;
	
	/**
	 * @var KalturaWebexAPIDropFolder
	 */
	protected $dropFolder;
	
	/**
	 * @var KalturaWebexAPIDropFolderFile
	 */
	protected $dropFolderFile;
	
	
	public function watchFolder(KalturaDropFolder $dropFolder)
	{
		$this->initDropFolderEngine($dropFolder);
		$recordingsList = $this->retrieveRecordingsList($dropFolder);
		if (!$recordingsList)
		{
			return;
		}
		$this->handleRecordingsList($recordingsList);
		$this->updateDropFolderLastFileTimestamp();
		$this->handleDropFolderFiles();
	}
	
	protected function initDropFolderEngine(KalturaDropFolder $dropFolder)
	{
		KalturaLog::info('Watching Webex drop folder [' . $dropFolder->id . ']');
		$this->webexClient = $this->initWebexClient($dropFolder);
		$this->dropFolder = $dropFolder;
		$this->lastFileTimestamp = $dropFolder->lastFileTimestamp;
	}
	
	protected function initWebexClient(KalturaDropFolder $dropFolder)
	{
		$refreshToken = isset($dropFolder->refreshToken) ? $dropFolder->refreshToken : null;
		$accessToken = isset($dropFolder->accessToken) ? $dropFolder->accessToken : null;
		$clientId = isset($dropFolder->clientId) ? $dropFolder->clientId : null;
		$clientSecret = isset($dropFolder->clientSecret) ? $dropFolder->clientSecret : null;
		$accessExpiresIn = isset($dropFolder->accessExpiresIn) ? $dropFolder->accessExpiresIn : null;
		return new kWebexAPIClient($dropFolder->baseURL, $refreshToken, $clientId, $clientSecret, $accessToken, $accessExpiresIn);
	}
	
	protected function retrieveRecordingsList(KalturaDropFolder $dropFolder)
	{
		
		$recordingsList = $this->webexClient->getRecordingsList($dropFolder->lastFileTimestamp);
		KalturaLog::info('Response from Webex recordings list:');
		KalturaLog::info(print_r($recordingsList));
		if (!isset($recordingsList['items']))
		{
			KalturaLog::debug('No items in response');
			return null;
		}
		return $recordingsList['items'];
	}
	
	protected function handleRecordingsList($recordingsList)
	{
		foreach ($recordingsList as $recordingItem)
		{
			if (!isset($recordingItem['id']))
			{
				KalturaLog::info('Error getting recording id from Webex');
				continue;
			}
			$recordingInfo = $this->webexClient->getRecording($recordingItem['id']);
			KalturaLog::info('Response from Webex recording info:');
			KalturaLog::info(print_r($recordingInfo));
			if (!isset($recordingInfo['topic']))
			{
				KalturaLog::info('Error getting recording name from Webex');
				continue;
			}
			$recordingFileName = $this->prepareNameForDropFolderFile($recordingInfo['topic']);
			
			if (!isset($recordingInfo['createTime']))
			{
				KalturaLog::info('Error getting recording create time from Webex');
				continue;
			}
			$createTime = strtotime($recordingInfo['createTime']);
			if ($createTime > $this->lastFileTimestamp)
			{
				$this->lastFileTimestamp = $createTime;
			}
			
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
	}
	
	protected function addDropFolderFile($recordingInfo)
	{
		try
		{
			$webexDropFolderFile = $this->allocateWebexDropFolderFile($recordingInfo);
			KalturaLog::info('Adding new WebexDropFolderFile:');
			KalturaLog::info(print_r($webexDropFolderFile, true));
			
			$dropFolderFile = $this->dropFolderFileService->add($webexDropFolderFile);
			return $dropFolderFile;
		}
		catch (Exception $e)
		{
			KalturaLog::err('Cannot add new drop folder file with name ['. $recordingInfo['topic'] .'] - ' . $e->getMessage());
			return null;
		}
	}
	
	protected function allocateWebexDropFolderFile($recordingInfo)
	{
		$webexDropFolderFile = new KalturaWebexAPIDropFolderFile();
		$webexDropFolderFile->dropFolderId = $this->dropFolder->id;
		$webexDropFolderFile->recordingId = $recordingInfo['id'];
		$webexDropFolderFile->fileName = $this->prepareNameForDropFolderFile($recordingInfo['topic']);
		$webexDropFolderFile->fileSize = $recordingInfo['sizeBytes'];
		$webexDropFolderFile->contentUrl = $recordingInfo['temporaryDirectDownloadLinks']['recordingDownloadLink'];
		$webexDropFolderFile->urlExpiry = strtotime($recordingInfo['temporaryDirectDownloadLinks']['expiration']);
		return $webexDropFolderFile;
	}
	
	protected function prepareNameForDropFolderFile($recordingName)
	{
		return $recordingName . '.webex';
	}
	
	protected function updateDropFolderLastFileTimestamp()
	{
		$updateDropFolder = new KalturaWebexAPIDropFolder();
		$updateDropFolder->lastFileTimestamp = $this->lastFileTimestamp;
		$this->dropFolderPlugin->dropFolder->update($this->dropFolder->id, $updateDropFolder);
		KalturaLog::debug("Last handled meeting time is: {$this->lastFileTimestamp}");
	}
	
	protected function handleDropFolderFiles()
	{
		$pager = new KalturaFilterPager();
		$pager->pageIndex = 0;
		$pager->pageSize = 500;
		if (KBatchBase::$taskConfig && KBatchBase::$taskConfig->params->pageSize)
		{
			$pager->pageSize = KBatchBase::$taskConfig->params->pageSize;
		}
		
		$fromCreatedAt = time() - self::DEFAULT_WEBEX_QUERY_TIME_RANGE;
		do
		{
			$pager->pageIndex++;
			$dropFolderFiles = $this->loadDropFolderFilesByPage($pager, $fromCreatedAt);
			foreach ($dropFolderFiles as $dropFolderFile)
			{
				KalturaLog::info("Handle drop folder file: {$dropFolderFile->fileName}");
				$this->handleExistingDropFolderFile($dropFolderFile);
			}
			
		} while (count($dropFolderFiles) >= $pager->pageSize);
	}
	
	protected function handleExistingDropFolderFile(KalturaDropFolderFile $dropFolderFile)
	{
		$fileSize = $dropFolderFile->fileSize;
		if (!$fileSize)
		{
			KalturaLog::info('Current file size is empty');
			return;
		}
		
		if ($dropFolderFile->status == KalturaDropFolderFileStatus::UPLOADING)
		{
			KalturaLog::info("Handle drop folder file (Status Uploading): {$dropFolderFile->fileName}");
			$this->handleUploadingDropFolderFile($dropFolderFile, $fileSize, 0);
		}
		else
		{
			$deleteTime = $dropFolderFile->updatedAt + $this->dropFolder->autoFileDeleteDays * 86400;
			if (($dropFolderFile->status == KalturaDropFolderFileStatus::HANDLED && $this->dropFolder->fileDeletePolicy != KalturaDropFolderFileDeletePolicy::MANUAL_DELETE && time() > $deleteTime) ||
				$dropFolderFile->status == KalturaDropFolderFileStatus::DELETED)
			{
				$this->purgeFile($dropFolderFile);
			}
		}
	}
	
	protected function purgeFile(KalturaDropFolderFile $dropFolderFile)
	{
		KalturaLog::info("Purging drop folder file: {$dropFolderFile->fileName}");
		$fullPath = $dropFolderFile->fileName;
		// client->delete file from webex
	}

	public function processFolder(KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data)
	{
		KBatchBase::impersonate($job->partnerId);
		
		$this->initProcessFolder($job, $data);
		list($entry, $flavorAsset) = $this->prepareEntryAndFlavorAsset($job->partnerId);
		$this->refreshDownloadUrl();
		$this->setContentOnEntry($entry, $flavorAsset);
		$this->updateDropFolderFile($entry->id);
		
		KBatchBase::unimpersonate();
	}
	
	protected function initProcessFolder(KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data)
	{
		KalturaLog::debug("Start processing Webex Folder [{$data->dropFolderId}]");
		if (!$data->contentMatchPolicy == KalturaDropFolderContentFileHandlerMatchPolicy::ADD_AS_NEW)
		{
			throw new kApplicativeException(KalturaDropFolderErrorCode::CONTENT_MATCH_POLICY_UNDEFINED, 'No content match policy is defined for drop folder');
		}
		
		$dropFolderFileId = $data->dropFolderFileIds;
		$this->dropFolderFile = $this->dropFolderFileService->get($dropFolderFileId);
		if (!$this->dropFolderFile)
		{
			throw new kApplicativeException(KalturaDropFolderErrorCode::DROP_FOLDER_APP_ERROR, ERROR_IN_CONTENT_PROCESSOR_MESSAGE);
		}
		
		$this->dropFolder = $this->dropFolderPlugin->dropFolder->get($data->dropFolderId);
		if (!$this->dropFolder->webexAPIVendorIntegration)
		{
			throw new kExternalException(KalturaDropFolderErrorCode::MISSING_CONFIG, DropFolderPlugin::MISSING_CONFIG_MESSAGE);
		}
	}
	
	protected function prepareEntryAndFlavorAsset($partnerId)
	{
		$webexBaseURL = $this->dropFolder->baseURL;
		//$zoomRecordingProcessor = new zoomMeetingProcessor($webexBaseURL, $dropFolder);
		$entry = $this->createEntryFromRecording($this->dropFolderFile, $partnerId, $this->dropFolder);
		//$this->setEntryCategory($entry, $recording->meetingMetadata->meetingId);
		//$this->handleParticipants($updatedEntry, $validatedUsers);
		//$entry = KBatchBase::$kClient->baseEntry->update($entry->id, $updatedEntry);
		
		$kFlavorAsset = new KalturaFlavorAsset();
		//$kFlavorAsset->tags = self::TAG_SOURCE;
		//$kFlavorAsset->flavorParamsId = self::SOURCE_FLAVOR_ID;
		$kFlavorAsset->fileExt = strtolower($this->dropFolderFile->fileExtension);
		$flavorAsset = KBatchBase::$kClient->flavorAsset->add($entry->id, $kFlavorAsset);
		
		return array($entry, $flavorAsset);
	}
	
	/**
	 * @param kalturaWebexAPIDropFolderFile $dropFolderFile
	 * @param string $ownerId
	 * @param $dropFolder
	 * @return KalturaBaseEntry
	 * @throws Exception
	 */
	protected function createEntryFromRecording($dropFolderFile, $ownerId, $dropFolder)
	{
		$newEntry = new KalturaMediaEntry();
		$newEntry->sourceType = KalturaSourceType::URL;
		$newEntry->mediaType = KalturaMediaType::VIDEO;
		$newEntry->description = $this->createEntryDescriptionFromRecording($dropFolderFile);
		$newEntry->name = $dropFolderFile->fileName;
		$newEntry->userId = $ownerId;
		$newEntry->conversionProfileId = $dropFolder->conversionProfileId;
		//$newEntry->adminTags = self::ADMIN_TAG_ZOOM;
		//$newEntry->referenceId = self::ZOOM_PREFIX . $dropFolerFile->meetingMetadata->uuid;
		KBatchBase::impersonate($this->dropFolder->partnerId);
		$kalturaEntry = KBatchBase::$kClient->baseEntry->add($newEntry);
		KBatchBase::unimpersonate();
		return $kalturaEntry;
	}
	
	/**
	 * @param KalturaZoomDropFolderFile $dropFolderFile
	 * @return string
	 */
	protected function createEntryDescriptionFromRecording($dropFolderFile)
	{
		//$meetingStartTime = gmdate("Y-m-d h:i:sa", $dropFolderFile->meetingMetadata->meetingStartTime);
		//return "Webex Recording ID: {$dropFolderFile->meetingMetadata->meetingId}\nUUID: {$dropFolderFile->meetingMetadata->uuid}\nMeeting Time: {$meetingStartTime}";
		return "Webex Recording";
	}
	
	protected function refreshDownloadUrl()
	{
		if ($this->dropFolderFile->urlExpiry < time() + kTimeConversion::MINUTE * 5)
		{
			KalturaLog::info("Refreshing download link for {$this->dropFolderFile->fileName}");
			$this->webexClient = $this->initWebexClient($this->dropFolder);
			$recordingInfo = $this->webexClient->getRecording($this->dropFolderFile->recordingId);
			$this->dropFolderFile->contentUrl = $recordingInfo['temporaryDirectDownloadLinks']['recordingDownloadLink'];
			$this->dropFolderFile->urlExpiry = strtotime($recordingInfo['temporaryDirectDownloadLinks']['expiration']);
		}
	}
	
	protected function setContentOnEntry($entry, $flavorAsset)
	{
		$resource = new KalturaUrlResource();
		$resource->url = $this->dropFolderFile->contentUrl;
		$resource->forceAsyncDownload = true;
		
		$assetParamsResourceContainer =  new KalturaAssetParamsResourceContainer();
		$assetParamsResourceContainer->resource = $resource;
		$assetParamsResourceContainer->assetParamsId = $flavorAsset->flavorParamsId;
		
		KBatchBase::$kClient->media->updateContent($entry->id, $resource);
	}
	
	function updateDropFolderFile($entryId)
	{
		$kWebexDropFolderFile = new KalturaWebexAPIDropFolderFile();
		$kWebexDropFolderFile->entryId = $entryId;
		$this->dropFolderFileService->update($this->dropFolderFile->id, $kWebexDropFolderFile);
		$this->dropFolderFileService->updateStatus($this->dropFolderFile->id, KalturaDropFolderFileStatus::HANDLED);
	}
}
