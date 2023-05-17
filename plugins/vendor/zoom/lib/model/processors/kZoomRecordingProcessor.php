<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.model
 */

abstract class kZoomRecordingProcessor extends kZoomProcessor
{
	const ADMIN_TAG_ZOOM = 'zoomentry';

	/**
	 * @var ZoomVendorIntegration
	 */
	protected $zoomIntegration;

	/**
	 * @var entry
	 */
	protected $mainEntry;
	
	/**
	 * @var string
	 */
	protected $zoomBaseUrl;

	/**
	 * kZoomRecordingProcessor constructor.
	 * @param string $zoomBaseUrl
	 */
	public function __construct($zoomBaseUrl)
	{
		$this->mainEntry = null;
		$this->zoomBaseUrl = $zoomBaseUrl;
		$this->zoomIntegration = ZoomHelper::getZoomIntegration();
		parent::__construct($zoomBaseUrl, $this->zoomIntegration->getAccountId(), $this->zoomIntegration->getRefreshToken(),
		                    $this->zoomIntegration->getAccessToken(), $this->zoomIntegration->getExpiresIn(), $this->zoomIntegration->getZoomAuthType());
	}

	/**
	 * @param kZoomRecording $recording
	 * @param kUser $dbUser
	 * @return bool
	 * @throws PropelException
	 * @throws kCoreException
	 */
	protected function wasEventHandled($recording, $dbUser)
	{
		$resourceReservation = new kResourceReservation(self::ZOOM_LOCK_TTL, true);
		if(!$resourceReservation->reserve($recording->uuid))
		{
			KalturaLog::debug("Recording {$recording->uuid} is being processed");
			return true;
		}

		$this->initUserPermissions($dbUser);
		if($this->getZoomEntryByRecordingId($recording->uuid))
		{
			KalturaLog::debug("Recording {$recording->uuid} already processed");
			return true;
		}

		return false;
	}

	/**
	 * @param kZoomEvent $event
	 * @throws kCoreException
	 * @throws PropelException
	 */
	public function handleRecordingVideoComplete($event)
	{
		/* @var kZoomRecording $recording */
		$recording = $event->object;
		$dbUser = $this->getEntryOwner($recording->hostEmail, $this->zoomIntegration);
		if($this->wasEventHandled($recording, $dbUser))
		{
			return;
		}

		$extraUsers = $this->getAdditionalUsers($recording->id, $dbUser->getPuserId());
		foreach ($recording->recordingFiles as $recordingFilesPerTimeSlot)
		{
			$this->mainEntry = null;
			foreach ($recordingFilesPerTimeSlot[kRecordingFileType::VIDEO] as $recordingFile)
			{
				$this->handleVideoRecord($recording, $dbUser, $extraUsers, $recordingFile, $event);
			}

			if (isset($recordingFilesPerTimeSlot[kRecordingFileType::CHAT]))
			{
				$chatFilesProcessor = new kZoomChatFilesProcessor($this->zoomBaseUrl, $this->zoomIntegration->getAccountId(),
											$this->zoomIntegration->getRefreshToken(), $this->zoomIntegration->getAccessToken(),
											$this->zoomIntegration->getExpiresIn(), $this->zoomIntegration->getZoomAuthType());

				foreach($recordingFilesPerTimeSlot[kRecordingFileType::CHAT] as $recordingFile)
				{
					$chatFilesProcessor->handleChatRecord($this->mainEntry, $recording, $recordingFile->download_url, $event->downloadToken, $dbUser);
				}
			}
		}
	}

	/**
	 * @param kZoomRecording $recording
	 * @param kuser $owner
	 * @param $validatedUsers
	 * @param kZoomRecordingFile $recordingFile
	 * @param kZoomEvent $event
	 * @return entry
	 * @throws PropelException
	 * @throws kCoreException
	 * @throws Exception
	 */
	protected function handleVideoRecord($recording, $owner, $validatedUsers, $recordingFile, $event)
	{
		$entry = $this->createEntryFromRecording($recording, $owner);
		if($this->mainEntry)
		{
			$entry->setParentEntryId($this->mainEntry->getId());
		}

		$this->setEntryCategory($entry);
		$this->handleParticipants($entry, $validatedUsers);
		$entry->save();

		if(!$this->mainEntry)
		{
			$this->mainEntry = $entry;
		}

		$url = $recordingFile->download_url;
		$headers = array("Authorization: Bearer {$event->downloadToken}");
		$redirectUrl = ZoomHelper::getRedirectUrl($url, $headers);
		$flavorAsset = kFlowHelper::createOriginalFlavorAsset($entry->getPartnerId(), $entry->getId(), $recordingFile->fileExtension);
		$jobData = new kImportJobData();
		$jobData->setUrlHeaders($headers);
		kJobsManager::addImportJob(null, $entry->getId(), $entry->getPartnerId(), $redirectUrl, $flavorAsset, null, $jobData);
		return $entry;
	}

	/**
	 * @param entry $entry
	 * @param array $validatedUsers
	 * @throws kCoreException
	 */
	protected function handleParticipants($entry, $validatedUsers)
	{
		$handleParticipantMode = $this->zoomIntegration->getHandleParticipantsMode();
		if ($validatedUsers && $handleParticipantMode != kHandleParticipantsMode::IGNORE)
		{
			switch ($handleParticipantMode)
			{
				case kHandleParticipantsMode::ADD_AS_CO_PUBLISHERS:
					$entry->setEntitledPusersPublish(implode(',', array_unique($validatedUsers)));
					break;
				case kHandleParticipantsMode::ADD_AS_CO_VIEWERS:
					$entry->setEntitledPusersView(implode(',', array_unique($validatedUsers)));
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

		foreach ($zoomUsers as $zoomUser)
		{
			/* @var $zoomUser kZoomUser */
			$dbUser = $this->getKalturaUser($partnerId, $zoomUser);
			if($dbUser)
			{
				if (strtolower($dbUser->getPuserId()) !== $userToExclude)
				{
					$validatedUsers[] = $dbUser->getPuserId();
				}
			}
			elseif($createIfNotFound)
			{
				kuserPeer::createKuserForPartner($partnerId, $zoomUser->getProcessedName());
				$validatedUsers[] = $zoomUser->getProcessedName();
			}
		}

		return $validatedUsers;
	}

	/**
	 * @param kZoomRecording $recording
	 * @return string
	 */
	protected function createEntryDescriptionFromRecording($recording)
	{
		return "Zoom Recording ID: {$recording->id}\nUUID: {$recording->uuid}\nMeeting Time: {$recording->startTime}GMT";
	}

	/**
	 * @param entry $entry
	 * @throws kCoreException
	 */
	protected abstract function setEntryCategory($entry);

	/**
	 * @param kZoomRecording $recording
	 * @param kuser $owner
	 * @return entry
	 * @throws Exception
	 */
	protected function createEntryFromRecording($recording, $owner)
	{
		$entry = new entry();
		$entry->setType(entryType::MEDIA_CLIP);
		$entry->setSourceType(EntrySourceType::URL);
		$entry->setMediaType(entry::ENTRY_MEDIA_TYPE_VIDEO);
		$entry->setDescription($this->createEntryDescriptionFromRecording($recording));
		$entry->setName($recording->topic);
		$entry->setPartnerId($owner->getPartnerId());
		$entry->setStatus(entryStatus::NO_CONTENT);
		$entry->setPuserId($owner->getPuserId());
		$entry->setKuserId($owner->getKuserId());
		$entry->setConversionProfileId(myPartnerUtils::getConversionProfile2ForPartner($owner->getPartnerId())->getId());
		$entry->setAdminTags(self::ADMIN_TAG_ZOOM);
		$entry->setReferenceID(self::ZOOM_PREFIX . $recording->uuid);
		if($this->zoomIntegration->getConversionProfileId())
		{
			$entry->setConversionProfileId($this->zoomIntegration->getConversionProfileId());
		}

		return $entry;
	}

	/**
	 * @param string $recordingId
	 * @param string $userToExclude
	 * @return array|null
	 */
	protected function getAdditionalUsers($recordingId, $userToExclude)
	{
		if ($this->zoomIntegration->getHandleParticipantsMode() == kHandleParticipantsMode::IGNORE || $this->zoomIntegration->getUserMatching() == kZoomUsersMatching::CMS_MATCHING)
		{
			return null;
		}

		$userToExclude = strtolower($userToExclude);
		kZoomOauth::getValidAccessTokenAndSave($this->zoomIntegration);
		$additionalUsersZoomResponse = $this->getAdditionalUsersFromZoom($recordingId);
		$additionalZoomUsers = $this->parseAdditionalUsers($additionalUsersZoomResponse);
		return $this->getValidatedUsers($additionalZoomUsers, $this->zoomIntegration->getPartnerId(), $this->zoomIntegration->getCreateUserIfNotExist(), $userToExclude);
	}

	protected abstract function getAdditionalUsersFromZoom($recordingId);

	protected abstract function parseAdditionalUsers($additionalUsersZoomResponse);
}