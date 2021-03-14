<?php
/**
 * @package plugins.ZoomDropFolder
 */

abstract class zoomRecordingProcessor extends zoomProcessor
{
	const ADMIN_TAG_ZOOM = 'zoomentry';
	
	/**
	 * @var KalturaMediaEntry
	 */
	protected $mainEntry;
	
	/**
	 * @var string
	 */
	protected $zoomBaseUrl;
	
	/**
	 * zoomRecordingProcessor constructor.
	 * @param string $zoomBaseUrl
	 * @param KalturaZoomDropFolder $folder
	 */
	public function __construct($zoomBaseUrl, KalturaZoomDropFolder $folder)
	{
		$this->mainEntry = null;
		$this->zoomBaseUrl = $zoomBaseUrl;
		parent::__construct($zoomBaseUrl, $folder);
	}
	
	/**
	 * @param kalturaZoomDropFolderFile $recording
	 * @return bool
	 * @throws PropelException
	 * @throws kCoreException
	 */
	protected function wasEventHandled($recording)
	{
		$resourceReservation = new kResourceReservation(self::ZOOM_LOCK_TTL, true);
		if(!$resourceReservation->reserve($recording->meetingMetadata->uuid))
		{
			KalturaLog::debug("Recording {$recording->meetingMetadata->uuid} is being processed");
			return true;
		}
		
		if($this->getZoomEntryByRecordingId($recording->meetingMetadata->uuid, $recording->partnerId))
		{
			KalturaLog::debug("Recording {$recording->meetingMetadata->uuid} already processed");
			return true;
		}
		
		return false;
	}
	
	/**
	 * @param KalturaZoomDropFolderFile $recording
	 * @throws kCoreException
	 * @throws PropelException
	 */
	public function handleRecordingVideoComplete($recording)
	{
		$hostId = $recording->meetingMetadata->hostId;
		$zoomUser = $this->zoomClient->retrieveZoomUser($hostId);
		$hostEmail = '';
		if(isset($zoomUser[self::EMAIL]) && !empty($zoomUser[self::EMAIL]))
		{
			$hostEmail = $zoomUser[self::EMAIL];
		}
		
		/* @var KalturaUser $kUser */
		$kUser = $this->getEntryOwner($hostEmail);
		if($this->wasEventHandled($recording))
		{
			return;
		}
		
		$extraUsers = $this->getAdditionalUsers($recording->meetingMetadata->meetingId, $kUser->id);
		if ($recording->recordingFile->fileType == kRecordingFileType::VIDEO)
		{
			$this->handleVideoRecord($recording, $kUser, $extraUsers);
		}
		else if($recording->recordingFile->fileType == kRecordingFileType::CHAT)
		{
			$chatFilesProcessor = new zoomChatFilesProcessor($this->zoomBaseUrl, $this->dropFolder);
			$chatFilesProcessor->handleChatRecord($this->mainEntry, $recording, $recording->recordingFile->downloadUrl);
		}
	}
	
	/**
	 * @param kalturaZoomDropFolderFile $recording
	 * @param KalturaUser $owner
	 * @param $validatedUsers
	 * @return KalturaMediaEntry
	 * @throws PropelException
	 * @throws kCoreException
	 * @throws Exception
	 */
	protected function handleVideoRecord($recording, $owner, $validatedUsers)
	{
		/* @var KalturaMediaEntry $entry*/
		$entry = $this->createEntryFromRecording($recording, $owner);
		
		$updatedEntry = new KalturaMediaEntry();
		if($this->mainEntry)
		{
			$updatedEntry->parentEntryId = $this->mainEntry->id;
		}
		
		$this->setEntryCategory($entry);
		$this->handleParticipants($updatedEntry, $validatedUsers);
		KBatchBase::impersonate($entry->partnerId);
		$entry = KBatchBase::$kClient->baseEntry->update($entry->id, $updatedEntry);
		
		if(!$this->mainEntry)
		{
			$this->mainEntry = $entry;
		}
		
		$kFlavorAsset = new KalturaFlavorAsset();
		$kFlavorAsset->tags = array(flavorParams::TAG_SOURCE);
		$kFlavorAsset->flavorParamsId = flavorParams::SOURCE_FLAVOR_ID;
		$kFlavorAsset->fileExt = $recording->recordingFile->fileExtension;
		$flavorAsset = KBatchBase::$kClient->flavorAsset->add($entry->id, $kFlavorAsset);
		
		$url = $recording->recordingFile->downloadUrl . self::URL_ACCESS_TOKEN . $this->dropFolder->accessToken;
		$resource = new KalturaUrlResource();
		$resource->url = $url;
		$resource->forceAsyncDownload = true;
		
		$assetParamsResourceContainer =  new KalturaAssetParamsResourceContainer();
		$assetParamsResourceContainer->resource = $resource;
		$assetParamsResourceContainer->assetParamsId = $flavorAsset->flavorParamsId;
		KBatchBase::$kClient->media->addContent($entry->id, $resource);
		KBatchBase::unimpersonate();
		return $entry;
	}
	
	/**
	 * @param KalturaMediaEntry $entry
	 * @param array $validatedUsers
	 * @throws kCoreException
	 */
	protected function handleParticipants($entry, $validatedUsers)
	{
		$handleParticipantMode = $this->dropFolder->zoomVendorIntegration->handleParticipantsMode;
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
			}
		}
	}
	
	protected function getValidatedUsers($zoomUsers, $partnerId, $createIfNotFound, $userToExclude)
	{
		$validatedUsers=array();
		if(!$zoomUsers)
		{
			return $zoomUsers;
		}
		KBatchBase::impersonate($partnerId);
		foreach ($zoomUsers as $zoomUser)
		{
			/* @var $zoomUser kZoomUser */
			/* @var $kUser KalturaUser */
			$kUser = $this->getKalturaUser($partnerId, $zoomUser);
			if($kUser)
			{
				if (strtolower($kUser->id) !== $userToExclude)
				{
					$validatedUsers[] = $kUser->id;
				}
			}
			elseif($createIfNotFound)
			{
				$this->createNewUser($partnerId, $zoomUser->getProcessedName());
				$validatedUsers[] = $zoomUser->getProcessedName();
			}
		}
		KBatchBase::unimpersonate();
		return $validatedUsers;
	}
	
	/**
	 * @param KalturaZoomDropFolderFile $recording
	 * @return string
	 */
	protected function createEntryDescriptionFromRecording($recording)
	{
		return "Zoom Recording ID: {$recording->meetingMetadata->meetingId}\nUUID: {$recording->meetingMetadata->uuid}\nMeeting Time: {$recording->meetingMetadata->meetingStartTime}";
	}
	
	/**
	 * @param KalturaMediaEntry $entry
	 * @throws kCoreException
	 */
	protected abstract function setEntryCategory($entry);
	
	/**
	 * @param kalturaZoomDropFolderFile $recording
	 * @param KalturaUser $owner
	 * @return entry
	 * @throws Exception
	 */
	protected function createEntryFromRecording($recording, $owner)
	{
		$newEntry = new KalturaMediaEntry();
		$newEntry->sourceType = KalturaSourceType::URL;
		$newEntry->mediaType = KalturaMediaType::VIDEO;
		$newEntry->description = $this->createEntryDescriptionFromRecording($recording);
		$newEntry->name = $recording->meetingMetadata->topic;
		$newEntry->partnerId = $owner->partnerId;
		$newEntry->status = KalturaEntryStatus::NO_CONTENT;
		$newEntry->userId = $owner->id;
		$newEntry->conversionProfileId = $this->dropFolder->conversionProfileId;
		$newEntry->adminTags = self::ADMIN_TAG_ZOOM;
		$newEntry->referenceId = self::ZOOM_PREFIX . $recording->meetingMetadata->uuid;
		KBatchBase::impersonate($owner->partnerId);
		$kalturaEntry = KBatchBase::$kClient->baseEntry->add($newEntry);
		KBatchBase::unimpersonate();
		return $kalturaEntry;
	}
	
	/**
	 * @param string $recordingId
	 * @param string $userToExclude
	 * @return array|null
	 */
	protected function getAdditionalUsers($recordingId, $userToExclude)
	{
		if ($this->dropFolder->zoomVendorIntegration->handleParticipantsMode == kHandleParticipantsMode::IGNORE ||
			$this->dropFolder->zoomVendorIntegration->userMatching == kZoomUsersMatching::CMS_MATCHING)
		{
			return null;
		}
		
		$userToExclude = strtolower($userToExclude);
		$additionalUsersZoomResponse = $this->getAdditionalUsersFromZoom($recordingId);
		$additionalZoomUsers = $this->parseAdditionalUsers($additionalUsersZoomResponse);
		return $this->getValidatedUsers($additionalZoomUsers, $this->dropFolder->partnerId, $this->dropFolder->zoomVendorIntegration->createUserIfNotExist,
		                                $userToExclude);
	}
	
	protected abstract function getAdditionalUsersFromZoom($recordingId);
	
	protected abstract function parseAdditionalUsers($additionalUsersZoomResponse);
	
}