<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.model
 */

abstract class kZoomRecordingProcessor extends kZoomProcessor
{
	const ADMIN_TAG_ZOOM = 'zoomentry';

	protected $zoomClient;

	/**
	 * kZoomRecordingProcessor constructor.
	 * @param string $zoomBaseUrl
	 */
	public function __construct($zoomBaseUrl)
	{
		$this->zoomClient = new kZoomClient($zoomBaseUrl);
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
		$zoomIntegration = ZoomHelper::getZoomIntegration();
		/* @var kZoomRecording $recording */
		$recording = $event->object;
		$dbUser = $this->getEntryOwner($recording->hostEmail, $zoomIntegration);
		if($this->wasEventHandled($recording, $dbUser))
		{
			return;
		}

		$extraUsers = $this->getAdditionalUsers($recording->id, $zoomIntegration, $dbUser->getPuserId());
		$entry = null;
		foreach ($recording->recordingFiles[kRecordingFileType::VIDEO] as $recordingFile)
		{
			$entry = $this->handleVideoRecord($recording, $dbUser, $zoomIntegration, $extraUsers, $recordingFile, $event);
		}

		if(isset($recording->recordingFiles[kRecordingFileType::CHAT]))
		{
			$chatFilesProcessor = new kZoomChatFilesProcessor();
			foreach ($recording->recordingFiles[kRecordingFileType::CHAT] as $recordingFile)
			{
				$chatFilesProcessor->handleChatRecord($entry, $recording, $recordingFile->download_url, $event->downloadToken, $dbUser);
			}
		}
	}

	/**
	 * @param kZoomRecording $recording
	 * @param kuser $owner
	 * @param $zoomIntegration
	 * @param $validatedUsers
	 * @param kZoomRecordingFile $recordingFile
	 * @param kZoomEvent $event
	 * @return entry
	 * @throws PropelException
	 * @throws kCoreException
	 * @throws Exception
	 */
	protected function handleVideoRecord($recording, $owner, $zoomIntegration, $validatedUsers, $recordingFile, $event)
	{
		$entry = $this->createEntryFromRecording($recording, $owner);
		$this->setEntryCategory($zoomIntegration, $entry);
		$this->handleParticipants($entry, $validatedUsers, $zoomIntegration);
		$entry->save();
		$url = $recordingFile->download_url . self::URL_ACCESS_TOKEN . $event->downloadToken;
		kJobsManager::addImportJob(null, $entry->getId(), $entry->getPartnerId(), $url);
		return $entry;
	}

	/**
	 * @param entry $entry
	 * @param array $validatedUsers
	 * @param ZoomVendorIntegration $zoomIntegration
	 * @throws kCoreException
	 */
	protected function handleParticipants($entry, $validatedUsers, $zoomIntegration)
	{
		$handleParticipantMode = $zoomIntegration->getHandleParticipantsMode();
		if ($validatedUsers && $handleParticipantMode != kHandleParticipantsMode::IGNORE)
		{
			switch ($handleParticipantMode)
			{
				case kHandleParticipantsMode::ADD_AS_CO_PUBLISHERS:
					$entry->setEntitledPusersPublish(implode(",", array_unique($validatedUsers)));
					break;
				case kHandleParticipantsMode::ADD_AS_CO_VIEWERS:
					$entry->setEntitledPusersView(implode(",", array_unique($validatedUsers)));
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
		return "Zoom Recording ID: {$recording->id}\nUUID: {$recording->uuid}\nMeeting Time: {$recording->startTime}";
	}

	/**
	 * @param ZoomVendorIntegration $zoomIntegration
	 * @param entry $entry
	 * @throws kCoreException
	 */
	protected abstract function setEntryCategory($zoomIntegration, $entry);

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
		return $entry;
	}

	/**
	 * @param string $recordingId
	 * @param ZoomVendorIntegration $zoomIntegration
	 * @param sting $userToExclude
	 * @return array|null
	 */
	protected function getAdditionalUsers($recordingId, $zoomIntegration, $userToExclude)
	{
		if ($zoomIntegration->getHandleParticipantsMode() == kHandleParticipantsMode::IGNORE)
		{
			return null;
		}

		$userToExclude = strtolower($userToExclude);
		$accessToken = kZoomOauth::getValidAccessToken($zoomIntegration);
		$additionalUsersZoomResponse = $this->getAdditionalUsersFromZoom($accessToken, $recordingId);
		$additionalZoomUsers = $this->parseAdditionalUsers($additionalUsersZoomResponse, $zoomIntegration);
		return $this->getValidatedUsers($additionalZoomUsers, $zoomIntegration->getPartnerId(), $zoomIntegration->getCreateUserIfNotExist(), $userToExclude);
	}

	protected abstract function getAdditionalUsersFromZoom($accessToken, $recordingId);

	protected abstract function parseAdditionalUsers($additionalUsersZoomResponse, $zoomIntegration);
}