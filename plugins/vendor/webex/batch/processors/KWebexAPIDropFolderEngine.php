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
	const KALTURA_WEBEX_API_DEFAULT_USER = 'KalturaWebexAPIDefault';
	
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
		$recordingsList = $this->retrieveRecordingsList();
		if ($recordingsList)
		{
			$this->handleRecordingsList($recordingsList);
		}
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
	
	protected function retrieveRecordingsList()
	{
		$startTime = $this->lastFileTimestamp;
		$endTime = $startTime + kTimeConversion::DAY;
		
		$recordingsList = $this->webexClient->getRecordingsList($startTime, $endTime);
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
				KalturaLog::warning('Error getting meeting host email from Webex, meeting id: ' . $meetingInfo['id']);
				continue;
			}
			$hostEmail = $meetingInfo['hostEmail'];
			
			if ($this->excludeRecordingIngestForUser($hostEmail))
			{
				KalturaLog::debug("The user [$hostEmail] is configured to not save recordings - Not processing");
				continue;
			}
			
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
			KalturaLog::info('Adding new WebexDropFolderFile: ' . print_r($webexDropFolderFile, true));
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
		
		$response = $this->webexClient->deleteRecording($dropFolderFile->recordingId, $dropFolderFile->hostEmail);
		if (!$response)
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
		$ownerId = $this->getEntryOwnerId($this->dropFolderFile->hostEmail);
		$entry = $this->createEntryFromRecording($ownerId);
		if (!$entry)
		{
			throw new kApplicativeException(KalturaDropFolderErrorCode::DROP_FOLDER_APP_ERROR, 'Failed to create new entry');
		}
		
		VendorHelper::addEntryToCategory($this->dropFolder->webexAPIVendorIntegration->webexCategory, $entry->id, $partnerId);
		$entry = $this->handleParticipants($entry, $ownerId);
		$flavorAsset = $this->createFlavorAssetForEntry($entry->id);
		
		return array($entry, $flavorAsset);
	}
	
	protected function getEntryOwnerId($hostEmail)
	{
		$partnerId = $this->dropFolder->partnerId;
		$vendorIntegration = $this->dropFolder->webexAPIVendorIntegration;
		
		$defaultUser = $vendorIntegration->defaultUserId;
		$createUserIfNotExist = $vendorIntegration->createUserIfNotExist;
		if ($hostEmail == '')
		{
			return $createUserIfNotExist ? self::KALTURA_WEBEX_API_DEFAULT_USER : $defaultUser;
		}
		$webexUser = new kVendorUser();
		$webexUser->setOriginalName($hostEmail);
		$webexUser->setProcessedName($this->processWebexUserName($hostEmail, $this->dropFolder->webexAPIVendorIntegration->userMatchingMode, $this->dropFolder->webexAPIVendorIntegration->userPostfix));
		/* @var $user KalturaUser */
		$user = self::getKalturaUser($partnerId, $webexUser);
		$userId = '';
		if ($user)
		{
			$userId = $user->id;
		}
		else
		{
			if ($vendorIntegration->createUserIfNotExist)
			{
				$userId = $webexUser->getProcessedName();
			}
			else if ($vendorIntegration->defaultUserId)
			{
				$userId = $vendorIntegration->defaultUserId;
			}
		}
		return $userId;
	}
	
	/**
	 * @param kalturaWebexAPIDropFolderFile $dropFolderFile
	 * @param string $ownerId
	 * @param $dropFolder
	 * @return KalturaBaseEntry
	 * @throws Exception
	 */
	protected function createEntryFromRecording($ownerId)
	{
		$dropFolderFile = $this->dropFolderFile;
		
		$newEntry = new KalturaMediaEntry();
		$newEntry->sourceType = KalturaSourceType::URL;
		$newEntry->mediaType = KalturaMediaType::VIDEO;
		$newEntry->description = $this->createEntryDescriptionFromRecording($dropFolderFile);
		$newEntry->name = $dropFolderFile->fileName;
		$newEntry->userId = $ownerId;
		$newEntry->conversionProfileId = $this->dropFolder->conversionProfileId;
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
	protected function handleParticipants($entry, $ownerId)
	{
		$handleParticipantMode = $this->dropFolder->webexAPIVendorIntegration->handleParticipantsMode;
		if ($handleParticipantMode == kHandleParticipantsMode::IGNORE)
		{
			return $entry;
		}
		
		$validatedUsers = $this->getAdditionalUsers($ownerId);
		if ($validatedUsers)
		{
			switch ($handleParticipantMode)
			{
				case kHandleParticipantsMode::ADD_AS_CO_PUBLISHERS:
					$entry->entitledUsersPublish = implode(',', array_unique($validatedUsers));
					break;
				case kHandleParticipantsMode::ADD_AS_CO_VIEWERS:
					$entry->entitledUsersView = implode(',', array_unique($validatedUsers));
					break;
				default:
					break;
			}
		}
		
		return $entry;
	}
	
	/**
	 * @return array|null
	 */
	protected function getAdditionalUsers($ownerId)
	{
		$meetingId = $this->dropFolderFile->meetingId;
		$hostEmail = $this->dropFolderFile->hostEmail;
		$participantsList = $this->webexClient->getMeetingParticipants($meetingId, $hostEmail);
		if (!isset($participantsList['items']))
		{
			KalturaLog::warning("Error getting meeting participants from Webex for meeting id [$meetingId], response: " . print_r($participantsList, true));
			throw new kApplicativeException(KalturaDropFolderErrorCode::DROP_FOLDER_APP_ERROR, DropFolderPlugin::MISSING_MEETING_PARTICIPANTS_INFO);
		}
		
		$userToExclude = strtolower($ownerId);
		$additionalWebexUsers = $this->parseAdditionalUsers($participantsList);
		return VendorHelper::getValidatedUsers($additionalWebexUsers, $this->dropFolder->partnerId, $this->dropFolder->webexAPIVendorIntegration->createUserIfNotExist, $userToExclude);
	}
	
	protected function parseAdditionalUsers($additionalUsersWebexResponse)
	{
		$usersList = array();
		
		foreach ($additionalUsersWebexResponse as $user)
		{
			if (!isset($user['email']) || !isset($user['host']))
			{
				KalturaLog::warning('Error getting information for participant, participant details: ' . print_r($user, true));
				throw new kApplicativeException(KalturaDropFolderErrorCode::DROP_FOLDER_APP_ERROR, DropFolderPlugin::MISSING_MEETING_PARTICIPANTS_INFO);
			}
			
			if (!$user['host'])
			{
				$usersList[] = $this->processWebexUserName($user['email'], $this->dropFolder->webexAPIVendorIntegration->userMatchingMode, $this->dropFolder->webexAPIVendorIntegration->userPostfix);
			}
		}
		
		return $usersList;
	}
	
	protected function processWebexUserName($userName, $userMatchingMode, $postFix)
	{
		switch ($userMatchingMode)
		{
			case kWebexAPIUsersMatching::ADD_POSTFIX:
				
				if (!kString::endsWith($userName, $postFix, false))
				{
					$userName = $userName . $postFix;
				}
				
				break;
			case kWebexAPIUsersMatching::REMOVE_POSTFIX:
				if (kString::endsWith($userName, $postFix, false))
				{
					$userName = substr($userName, 0, strlen($userName) - strlen($postFix));
				}
				
				break;
			case kWebexAPIUsersMatching::DO_NOT_MODIFY:
			default:
				break;
		}
		
		return $userName;
	}
	
	protected function excludeRecordingIngestForUser($hostEmail)
	{
		$groupParticipationType = $this->dropFolder->webexAPIVendorIntegration->groupParticipationType;
		$optInGroupNames = explode("\r\n", $this->dropFolder->webexAPIVendorIntegration->optInGroupNames);
		$optOutGroupNames = explode("\r\n", $this->dropFolder->webexAPIVendorIntegration->optOutGroupNames);
		$partnerId = $this->dropFolder->partnerId;
		
		$userId = $this->getEntryOwnerId($hostEmail);
		if (!$userId)
		{
			KalturaLog::err('Could not find user');
			return true;
		}
		
		if ($groupParticipationType == kWebexAPIGroupParticipationType::OPT_IN)
		{
			KalturaLog::debug('Account is configured to OPT IN the users that are members of the following groups ['.print_r($optInGroupNames, true).']');
			return VendorHelper::isUserNotMemberOfGroups($userId, $partnerId, $optInGroupNames);
		}
		elseif ($groupParticipationType == kWebexAPIGroupParticipationType::OPT_OUT)
		{
			KalturaLog::debug('Account is configured to OPT OUT the users that are members of the following groups ['.print_r($optOutGroupNames, true).']');
			return !VendorHelper::isUserNotMemberOfGroups($userId, $partnerId, $optOutGroupNames);
		}
	}
	
	protected function createFlavorAssetForEntry($entryId)
	{
		$kFlavorAsset = new KalturaFlavorAsset();
		$kFlavorAsset->tags = self::TAG_SOURCE;
		$kFlavorAsset->flavorParamsId = self::SOURCE_FLAVOR_ID;
		$kFlavorAsset->fileExt = strtolower($this->dropFolderFile->fileExtension);
		return KBatchBase::$kClient->flavorAsset->add($entryId, $kFlavorAsset);
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
