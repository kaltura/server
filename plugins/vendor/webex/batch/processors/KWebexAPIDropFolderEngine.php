<?php
/**
 * @package plugins.WebexAPIDropFolder
 */
class KWebexAPIDropFolderEngine extends KDropFolderFileTransferEngine
{
	const DEFAULT_WEBEX_QUERY_TIME_RANGE = 259200; // 3 days
	const ADMIN_TAG_WEBEX = 'webex_api_entry';
	const WEBEX_PREFIX = 'Webex_';
	const TAG_SOURCE = "source";
	const SOURCE_FLAVOR_ID = 0;
	
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
		$nextPageLink = null;
		do
		{
			$recordingsList = $this->retrieveRecordingsList($nextPageLink);
			if ($recordingsList)
			{
				$this->handleRecordingsList($recordingsList);
			}
			$nextPageLink = $this->webexClient->getNextPageLinkFromLastRequest();
		}
		while ($nextPageLink);
		
		$this->updateDropFolderLastFileTimestamp();
		$this->handleDropFolderFiles();
	}
	
	protected function initDropFolderEngine(KalturaDropFolder $dropFolder)
	{
		KalturaLog::info('Watching Webex drop folder [' . $dropFolder->id . ']');
		$this->dropFolder = $dropFolder;
		$this->webexClient = $this->initWebexClient();
		$this->lastFileTimestamp = $dropFolder->lastFileTimestamp;
	}
	
	protected function initWebexClient()
	{
		$refreshToken = isset($this->dropFolder->refreshToken) ? $this->dropFolder->refreshToken : null;
		$accessToken = isset($this->dropFolder->accessToken) ? $this->dropFolder->accessToken : null;
		$clientId = isset($this->dropFolder->clientId) ? $this->dropFolder->clientId : null;
		$clientSecret = isset($this->dropFolder->clientSecret) ? $this->dropFolder->clientSecret : null;
		$accessExpiresIn = isset($this->dropFolder->accessExpiresIn) ? $this->dropFolder->accessExpiresIn : null;
		return new kWebexAPIClient($this->dropFolder->baseURL, $refreshToken, $clientId, $clientSecret, $accessToken, $accessExpiresIn);
	}
	
	protected function retrieveRecordingsList($directLink)
	{
		if ($directLink)
		{
			$recordingsList = $this->webexClient->getRecordingsListFromDirectLink($directLink);
		}
		else
		{
			$startTime = $this->lastFileTimestamp;
			$endTime = $startTime + kTimeConversion::DAY;
			$recordingsList = $this->webexClient->getRecordingsList($startTime, $endTime);
		}
		
		if (!isset($recordingsList['items']))
		{
			
			KalturaLog::info('No recordings in response');
			return null;
		}
		KalturaLog::info('Response from Webex with ' . count($recordingsList['items']) . ' recordings');
		return $recordingsList['items'];
	}
	
	protected function handleRecordingsList($recordingsList)
	{
		foreach ($recordingsList as $recordingItem)
		{
			if (!isset($recordingItem['id']))
			{
				KalturaLog::warning('Error getting recording id from Webex');
				continue;
			}
			if (!isset($recordingItem['meetingId']))
			{
				KalturaLog::warning('Error getting meeting id from Webex, recording id: ' . $recordingItem['id']);
				continue;
			}
			$meetingInfo = $this->webexClient->getMeeting($recordingItem['meetingId']);
			
			if (!isset($meetingInfo['hostEmail']))
			{
				KalturaLog::warning('Error getting meeting host email from Webex, meeting id: ' . $recordingItem['meetingId']);
				continue;
			}
			$hostEmail = $meetingInfo['hostEmail'];
			$recordingInfo = $this->webexClient->getRecording($recordingItem['id'], $hostEmail);
			
			if (!isset($recordingInfo['topic']))
			{
				KalturaLog::warning('Error getting recording name from Webex, recording id: ' . $recordingItem['id'], ', response: ' . print_r($recordingItem, true));
				continue;
			}
			KalturaLog::info('Response from Webex with recording info for: ' . $recordingInfo['topic']);
			$recordingFileName = $this->prepareNameForDropFolderFile($recordingInfo['topic']);
			
			if (!isset($recordingInfo['createTime']))
			{
				KalturaLog::warning('Error getting recording create time from Webex, recording id: ' . $recordingItem['id']);
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
				$this->addDropFolderFile($recordingInfo, $hostEmail);
			}
			else
			{
				KalturaLog::info("File already exists for: $recordingFileName");
			}
		}
	}
	
	protected function addDropFolderFile($recordingInfo, $hostEmail)
	{
		try
		{
			$webexDropFolderFile = $this->allocateWebexDropFolderFile($recordingInfo, $hostEmail);
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
	
	protected function allocateWebexDropFolderFile($recordingInfo, $hostEmail)
	{
		$webexDropFolderFile = new KalturaWebexAPIDropFolderFile();
		$webexDropFolderFile->dropFolderId = $this->dropFolder->id;
		$webexDropFolderFile->recordingId = $recordingInfo['id'];
		$webexDropFolderFile->fileName = $this->prepareNameForDropFolderFile($recordingInfo['topic']);
		$webexDropFolderFile->fileSize = $recordingInfo['sizeBytes'];
		if (!isset($recordingInfo['temporaryDirectDownloadLinks']))
		{
			throw new kApplicativeException(KalturaDropFolderErrorCode::DROP_FOLDER_APP_ERROR, DropFolderPlugin::MISSING_RECORDING_INFO);
		}
		$webexDropFolderFile->contentUrl = $recordingInfo['temporaryDirectDownloadLinks']['recordingDownloadLink'];
		$webexDropFolderFile->urlExpiry = strtotime($recordingInfo['temporaryDirectDownloadLinks']['expiration']);
		$webexDropFolderFile->meetingId = $recordingInfo['meetingId'];
		$webexDropFolderFile->recordingStartTime = strtotime($recordingInfo['timeRecorded']);
		$webexDropFolderFile->hostEmail = $hostEmail;
		return $webexDropFolderFile;
	}
	
	protected function prepareNameForDropFolderFile($recordingName)
	{
		return $recordingName . '.webex';
	}
	
	protected function updateDropFolderLastFileTimestamp()
	{
		if ($this->lastFileTimestamp == $this->dropFolder->lastFileTimestamp && $this->lastFileTimestamp + kTimeConversion::DAY < time())
		{
			$this->lastFileTimestamp = $this->lastFileTimestamp + kTimeConversion::DAY;
		}
		
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
			$deleteTime = $dropFolderFile->updatedAt + $this->dropFolder->autoFileDeleteDays * kTimeConversion::DAY;
			if (($dropFolderFile->status == KalturaDropFolderFileStatus::HANDLED && $this->dropFolder->fileDeletePolicy != KalturaDropFolderFileDeletePolicy::MANUAL_DELETE && time() > $deleteTime) ||
				$dropFolderFile->status == KalturaDropFolderFileStatus::DELETED)
			{
				$this->purgeFile($dropFolderFile);
			}
		}
	}
	
	protected function purgeFile(KalturaDropFolderFile $dropFolderFile)
	{
		/** @var KalturaWebexApiDropFolderFile $dropFolderFile */
		KalturaLog::info("Purging drop folder file: {$dropFolderFile->fileName}");
		$fullPath = $dropFolderFile->fileName;
		
		$wasDeleteSuccessful = $this->webexClient->deleteRecording($dropFolderFile->recordingId, $dropFolderFile->hostEmail);
		if (!$wasDeleteSuccessful)
		{
			$this->handleFileError($dropFolderFile->id, KalturaDropFolderFileStatus::ERROR_DELETING, KalturaDropFolderFileErrorCode::ERROR_DELETING_FILE,
				DropFolderPlugin::ERROR_DELETING_FILE_MESSAGE. '['.$fullPath.']');
		}
		$this->handleFilePurged($dropFolderFile->id);
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
		try
		{
			$this->dropFolderFile = $this->dropFolderFileService->get($dropFolderFileId);
			if (!$this->dropFolderFile)
			{
				throw new kApplicativeException(KalturaDropFolderErrorCode::DROP_FOLDER_APP_ERROR, DropFolderPlugin::ERROR_IN_CONTENT_PROCESSOR_MESSAGE);
			}
			
			$this->dropFolder = $this->dropFolderPlugin->dropFolder->get($data->dropFolderId);
			if (!$this->dropFolder->webexAPIVendorIntegration)
			{
				throw new kExternalException(KalturaDropFolderErrorCode::MISSING_CONFIG, DropFolderPlugin::MISSING_CONFIG_MESSAGE);
			}
		}
		catch (Exception $e)
		{
			KalturaLog::err('Failed to initialize processing of Webex drop folder: ' . $e->getMessage());
			return null;
		}
	}
	
	protected function prepareEntryAndFlavorAsset($partnerId)
	{
		$entry = $this->createEntryFromRecording($this->dropFolderFile, $partnerId, $this->dropFolder);
		if (!$entry)
		{
			throw new kApplicativeException(KalturaDropFolderErrorCode::DROP_FOLDER_APP_ERROR, 'Failed to create new entry');
		}
		VendorHelper::addEntryToCategory($this->dropFolder->webexAPIVendorIntegration->webexCategory, $entry->id, $partnerId);
		
		$kFlavorAsset = new KalturaFlavorAsset();
		$kFlavorAsset->tags = self::TAG_SOURCE;
		$kFlavorAsset->flavorParamsId = self::SOURCE_FLAVOR_ID;
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
		$newEntry->adminTags = self::ADMIN_TAG_WEBEX;
		$newEntry->referenceId = self::WEBEX_PREFIX . $dropFolderFile->id;
		KBatchBase::impersonate($this->dropFolder->partnerId);
		$kalturaEntry = KBatchBase::$kClient->baseEntry->add($newEntry);
		KBatchBase::unimpersonate();
		return $kalturaEntry;
	}
	
	/**
	 * @param KalturaWebexAPIDropFolderFile $dropFolderFile
	 * @return string
	 */
	protected function createEntryDescriptionFromRecording($dropFolderFile)
	{
		$recordingStartTime = gmdate("Y-m-d h:i:sa", $dropFolderFile->recordingStartTime);
		return "Webex Meeting ID: {$dropFolderFile->meetingId}\nRecording Time: {$recordingStartTime} UTC";
	}
	
	/**
	 * @param KalturaMediaEntry $entry
	 * @param array $validatedUsers
	 * @throws kCoreException
	 */
	protected function handleParticipants($entry)
	{
		//$validatedUsers = $this->getAdditionalUsers($this->dropFolderFile->meetingId);
		$validatedUsers = array();
		
		$handleParticipantMode = $this->dropFolder->webexAPIVendorIntegration->handleParticipantsMode;
		if ($validatedUsers && $handleParticipantMode != kHandleParticipantsMode::IGNORE)
		{
			switch ($handleParticipantMode)
			{
				case kHandleParticipantsMode::ADD_AS_CO_PUBLISHERS:
					$entry->entitledUsersPublish = implode(',', array_unique($validatedUsers));
					break;
				case kHandleParticipantsMode::ADD_AS_CO_VIEWERS:
					$entry->entitledUsersView = implode(',', array_unique($validatedUsers));
					break;
				case kHandleParticipantsMode::IGNORE:
				default:
					break;
			}
		}
	}

	protected function refreshDownloadUrl()
	{
		$tokenExpiryGrace = kConf::getArrayValue(WebexAPIDropFolderPlugin::CONFIGURATION_DOWNLOAD_EXPIRY_GRACE, WebexAPIDropFolderPlugin::CONFIGURATION_WEBEX_ACCOUNT_PARAM, WebexAPIDropFolderPlugin::CONFIGURATION_VENDOR_MAP, 600);
		if (! ($this->dropFolderFile->urlExpiry < time() + $tokenExpiryGrace))
		{
			return;
		}
		
		KalturaLog::info("Refreshing download link for {$this->dropFolderFile->fileName}");
		$this->webexClient = $this->initWebexClient();
		$recordingInfo = $this->webexClient->getRecording($this->dropFolderFile->recordingId, $this->dropFolderFile->hostEmail);
		
		if (!isset($recordingInfo['temporaryDirectDownloadLinks']))
		{
			KalturaLog::warning('Error getting download link for recording, response from Webex: ' . print_r($recordingInfo, true));
			throw new kApplicativeException(KalturaDropFolderErrorCode::DROP_FOLDER_APP_ERROR, DropFolderPlugin::MISSING_RECORDING_INFO);
		}
		$this->dropFolderFile->contentUrl = $recordingInfo['temporaryDirectDownloadLinks']['recordingDownloadLink'];
		$this->dropFolderFile->urlExpiry = strtotime($recordingInfo['temporaryDirectDownloadLinks']['expiration']);
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
		try
		{
			$this->dropFolderFileService->update($this->dropFolderFile->id, $kWebexDropFolderFile);
			$this->dropFolderFileService->updateStatus($this->dropFolderFile->id, KalturaDropFolderFileStatus::HANDLED);
		}
		catch (Exception $e)
		{
			KalturaLog::err('Failed to update drop folder file: ' . $e->getMessage());
			return null;
		}
	}
}
