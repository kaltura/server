<?php
/**
 * @package plugins.ZoomDropFolder
 */

abstract class zoomRecordingProcessor extends zoomProcessor
{
	const ADMIN_TAG_ZOOM = 'zoomentry';
	const TAG_SOURCE = "source";
	const SOURCE_FLAVOR_ID = 0;

	/** ZOOM fields */
	const SETTINGS = 'settings';
	const ALTERNATIVE_HOSTS = 'alternative_hosts';
	const COHOST_ROLE = 'cohost';
	const NEXT_PAGE_TOKEN = 'next_page_token';

	/**
	 * @var KalturaMediaEntry
	 */
	public $mainEntry;
	
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
	 * @param KalturaZoomDropFolderFile $recording
	 * @return KalturaMediaEntry
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

		$ownerId = ZoomBatchUtils::getEntryOwnerId($hostEmail, $this->dropFolder->partnerId, $this->dropFolder->zoomVendorIntegration, $this->zoomClient);
		$validatedHosts = $this->getValidatedHosts($recording->meetingMetadata->meetingId, $ownerId);
		$extraUsers = $this->getAdditionalUsers($recording->meetingMetadata->meetingId, $ownerId);
		if (in_array($recording->recordingFile->fileType, array(KalturaRecordingFileType::VIDEO, KalturaRecordingFileType::AUDIO)))
		{
			$entry = $this->handleVideoRecord($recording, $ownerId, $extraUsers, $validatedHosts);
		}
		else if($recording->recordingFile->fileType == KalturaRecordingFileType::CHAT)
		{
			$chatFilesProcessor = new zoomChatFilesProcessor($this->zoomBaseUrl, $this->dropFolder);
			$chatFilesProcessor->handleChatRecord($this->mainEntry, $recording);
			$entry = $this->mainEntry;
		}
		return $entry;
	}
	
	/**
	 * @param kalturaZoomDropFolderFile $recording
	 * @param string $ownerId
	 * @param $validatedUsers
	 * @return KalturaMediaEntry
	 * @throws PropelException
	 * @throws kCoreException
	 * @throws Exception
	 */
	protected function handleVideoRecord($recording, $ownerId, $validatedUsers, $validatedHosts)
	{
		/* @var KalturaMediaEntry $entry*/
		if (!$recording->isParentEntry)
		{
			$entry = $this->createEntryFromRecording($recording, $ownerId);
		}
		else
		{
			$entry = $this->updateParentEntry($recording, $ownerId);
		}
		
		$updatedEntry = new KalturaMediaEntry();
		if($this->mainEntry)
		{
			if (!$recording->isParentEntry)
			{
				$updatedEntry->parentEntryId = $this->mainEntry->id;
			}
			else
			{
				$this->setEntryCategory($entry, $recording->meetingMetadata->meetingId);
			}
		}

		$this->handleParticipants($updatedEntry, $validatedUsers, $validatedHosts);
		KBatchBase::impersonate($entry->partnerId);
		$entry = KBatchBase::$kClient->baseEntry->update($entry->id, $updatedEntry);
		
		$kFlavorAsset = new KalturaFlavorAsset();
		$kFlavorAsset->tags = self::TAG_SOURCE;
		$kFlavorAsset->flavorParamsId = self::SOURCE_FLAVOR_ID;
		$kFlavorAsset->fileExt = strtolower($recording->recordingFile->fileExtension);
		$flavorAsset = KBatchBase::$kClient->flavorAsset->add($entry->id, $kFlavorAsset);
		
		$resource = new KalturaUrlResource();
		list($redirectUrl, $urlHeaders) = $this->getZoomRedirectUrlFromFile($recording);
		$resource->url = $redirectUrl;
		$resource->forceAsyncDownload = true;
		$resource->urlHeaders = $urlHeaders;
		
		$assetParamsResourceContainer =  new KalturaAssetParamsResourceContainer();
		$assetParamsResourceContainer->resource = $resource;
		$assetParamsResourceContainer->assetParamsId = $flavorAsset->flavorParamsId;
		KBatchBase::$kClient->media->updateContent($entry->id, $resource);
		$this->approveEntryIfNeeded($recording->parentEntryId);
		KBatchBase::unimpersonate();
		return $entry;
	}
	
	public function addEntryToCategory($categoryName, $entryId)
	{
		$categoryId = $this->findCategoryIdByName($categoryName);
		if ($categoryId)
		{
			$this->addCategoryEntry($categoryId, $entryId);
		}
	}
	
	public function findCategoryIdByName($categoryName)
	{
		$isFullPath = $this->isFullPath($categoryName);
		
		$categoryFilter = new KalturaCategoryFilter();
		if ($isFullPath)
		{
			$categoryFilter->fullNameEqual = $categoryName;
		}
		else
		{
			$categoryFilter->nameOrReferenceIdStartsWith = $categoryName;
		}
		
		$categoryResponse = KBatchBase::$kClient->category->listAction($categoryFilter, new KalturaFilterPager());
		$categoryId = null;
		if ($isFullPath)
		{
			if ($categoryResponse->objects && count($categoryResponse->objects) == 1)
			{
				$categoryId = $categoryResponse->objects[0]->id;
			}
		}
		else
		{
			$categoryIds = array();
			foreach ($categoryResponse->objects as $category)
			{
				if ($category->name === $categoryName)
				{
					$categoryIds[] = $category->id;
				}
			}
			$categoryId = (count($categoryIds) == 1) ? $categoryIds[0] : null;
		}
		return $categoryId;
	}
	
	public function isFullPath($categoryName)
	{
		$numCategories = count(explode('>', $categoryName));
		return ($numCategories > 1);
	}
	
	public static function addCategoryEntry($categoryId, $entryId)
	{
		$categoryEntry = new KalturaCategoryEntry();
		$categoryEntry->categoryId = $categoryId;
		$categoryEntry->entryId = $entryId;
		KBatchBase::$kClient->categoryEntry->add($categoryEntry);
	}
	
	/**
	 * @param KalturaMediaEntry $entry
	 * @param array $validatedUsers
	 * @param array $validatedHosts
	 * @throws kCoreException
	 */
	protected function handleParticipants($entry, $validatedUsers, $validatedHosts)
	{
		$entitledUsersPublish = array();
		if($validatedHosts)
		{
			$entitledUsersPublish = $validatedHosts;
			$entry->entitledUsersPublish = implode(',', array_unique($validatedHosts));
			$entry->entitledUsersEdit = implode(',', array_unique($validatedHosts));
			KalturaLog::debug("Adding co-hosts and alternative hosts as entitled user publisher and editor, users: " . print_r($validatedHosts, true));
		}

		$handleParticipantMode = $this->dropFolder->zoomVendorIntegration->handleParticipantsMode;
		if ($validatedUsers && $handleParticipantMode != kHandleParticipantsMode::IGNORE)
		{
			switch ($handleParticipantMode)
			{
				case kHandleParticipantsMode::ADD_AS_CO_PUBLISHERS:
					$entry->entitledUsersPublish = implode(',', array_unique(array_merge($validatedUsers, $entitledUsersPublish)));
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
			$kUser = ZoomBatchUtils::getKalturaUser($partnerId, $zoomUser);
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
		$meetingStartTime = gmdate("Y-m-d h:i:sa", $recording->meetingMetadata->meetingStartTime);
		return "Zoom Recording ID: {$recording->meetingMetadata->meetingId}\nUUID: {$recording->meetingMetadata->uuid}\nMeeting Time: {$meetingStartTime}GMT";
	}
	
	/**
	 * @param KalturaMediaEntry $entry
	 * @param string $meetingId
	 * @throws kCoreException
	 */
	protected abstract function setEntryCategory($entry, $meetingId);
	
	/**
	 * @param kalturaZoomDropFolderFile $recording
	 * @param string $ownerId
	 * @return entry
	 * @throws Exception
	 */
	protected function createEntryFromRecording($recording, $ownerId)
	{
		$newEntry = new KalturaMediaEntry();
		$newEntry->sourceType = KalturaSourceType::URL;
		if ($recording->recordingFile->fileType == KalturaRecordingFileType::AUDIO)
		{
			$newEntry->mediaType = KalturaMediaType::AUDIO;
		}
		else
		{
			$newEntry->mediaType = KalturaMediaType::VIDEO;
		}
		$newEntry->description = $this->createEntryDescriptionFromRecording($recording);
		$newEntry->name = $recording->meetingMetadata->topic;
		$newEntry->userId = $ownerId;
		$newEntry->conversionProfileId = $this->dropFolder->conversionProfileId;
		$newEntry->adminTags = self::ADMIN_TAG_ZOOM;
		$newEntry->referenceId = self::ZOOM_PREFIX . $recording->meetingMetadata->uuid;
		KBatchBase::impersonate($this->dropFolder->partnerId);
		$kalturaEntry = KBatchBase::$kClient->baseEntry->add($newEntry);
		KBatchBase::unimpersonate();
		return $kalturaEntry;
	}
	
	/**
	 * @param kalturaZoomDropFolderFile $recording
	 * @param string $ownerId
	 * @return entry
	 * @throws Exception
	 */
	protected function updateParentEntry($recording, $ownerId)
	{
		$updatedEntry = new KalturaMediaEntry();
		$updatedEntry->description = $this->createEntryDescriptionFromRecording($recording);
		$updatedEntry->name = $recording->meetingMetadata->topic;
		$updatedEntry->userId = $ownerId;
		$updatedEntry->adminTags = self::ADMIN_TAG_ZOOM;
		KBatchBase::impersonate($this->dropFolder->partnerId);
		$kalturaEntry = KBatchBase::$kClient->baseEntry->update($recording->parentEntryId, $updatedEntry);
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
			$this->dropFolder->zoomVendorIntegration->zoomUserMatchingMode == kZoomUsersMatching::CMS_MATCHING)
		{
			return null;
		}
		
		$userToExclude = strtolower($userToExclude);
		$additionalUsersZoomResponse = $this->getAdditionalUsersFromZoom($recordingId);
		$additionalZoomUsers = $this->parseAdditionalUsers($additionalUsersZoomResponse);
		return $this->getValidatedUsers($additionalZoomUsers, $this->dropFolder->partnerId, $this->dropFolder->zoomVendorIntegration->createUserIfNotExist,
		                                $userToExclude);
	}

	/**
	 * @param string $recordingId
	 * @param string $userToExclude
	 * @return array|null
	 */
	protected function getValidatedHosts($recordingId, $userToExclude)
	{
		$userToExclude = strtolower($userToExclude);
		$zoomCoHosts = $this->getCoHostsEmails($recordingId);
		$zoomAlternativeHosts = $this->getAlternativeHostsEmails($recordingId);
		$zoomHosts = $zoomCoHosts ? array_merge($zoomCoHosts, $zoomAlternativeHosts) : $zoomAlternativeHosts;
		$zoomHosts = $zoomHosts ? $zoomHosts : null;
		return $this->getValidatedUsers($zoomHosts, $this->dropFolder->partnerId, $this->dropFolder->zoomVendorIntegration->createUserIfNotExist,
										$userToExclude);
	}

	protected function getAlternativeHostsEmails($recordingId)
	{
		$data = $this->getAlternativeHostsData($recordingId);
		if(!isset($data[self::SETTINGS]) || !isset($data[self::SETTINGS][self::ALTERNATIVE_HOSTS]))
		{
			KalturaLog::debug("Found the following alternative hosts [" . $data[self::SETTINGS][self::ALTERNATIVE_HOSTS] . "]");
			$alternativeHostsEmails = explode(";", $data[self::SETTINGS][self::ALTERNATIVE_HOSTS]);
			return $this->parseZoomEmails($alternativeHostsEmails);
		}
		return array();
	}

	protected function getCoHostsEmails($recordingId)
	{
		$coHostsEmails = array();
		$pageIndex = 0;
		$maxPages = 10;
		$maxPageSize = 300;
		$nextPageToken = '';
		do
		{
			$metricsParticipants = $this->getCoHostsData($recordingId, $maxPageSize, $nextPageToken);
			$participants = new kZoomParticipants();
			$participants->parseData($metricsParticipants, self::COHOST_ROLE);
			$coHostsEmails = array_merge($coHostsEmails, $participants->getParticipantsEmails());
			$pageIndex++;
			$nextPageToken = $metricsParticipants && $metricsParticipants[self::NEXT_PAGE_TOKEN] ?
				$metricsParticipants[self::NEXT_PAGE_TOKEN] : '';

		} while ($nextPageToken !== '' && $pageIndex < $maxPages);

		KalturaLog::debug("Found the following co-hosts " . print_r($coHostsEmails, true));
		return $this->parseZoomEmails($coHostsEmails);
	}

	protected function parseZoomEmails($emails)
	{
		$result = array();
		if($emails)
		{
			$emails = array_filter(array_unique($emails), 'trim');
			KalturaLog::debug('Found the following emails: ' . implode(", ", $emails));
			foreach ($emails as $email)
			{
				$zoomUser = new kZoomUser();
				$zoomUser->setOriginalName($email);
				$zoomUser->setProcessedName(ZoomBatchUtils::processZoomUserName($email, $this->dropFolder->zoomVendorIntegration, $this->zoomClient));
				$result[] = $zoomUser;
			}
		}
		else
		{
			$result = null;
		}
		return $result;
	}

	protected abstract function getAdditionalUsersFromZoom($recordingId);
	
	protected abstract function parseAdditionalUsers($additionalUsersZoomResponse);

	protected abstract function getAlternativeHostsData($recordingId);

	protected abstract function getCoHostsData($recordingId, $pageSize, $nextPageToken);

	protected function approveEntryIfNeeded($parentEntryId)
	{
		$parentEntry =  KBatchBase::$kClient->baseEntry->get($parentEntryId);
		if ($parentEntry && $parentEntry->replacementStatus  == KalturaEntryReplacementStatus::NOT_READY_AND_NOT_APPROVED)
		{
			KBatchBase::$kClient->media->approveReplace($parentEntryId);
		}
	}
}