<?php
/**
 * @package plugins.venodr
 * @subpackage model.zoom
 */

class kZoomEngine
{
	const ADMIN_TAG_ZOOM = 'zoomentry';
	const PHP_INPUT = 'php://input';
	const URL_ACCESS_TOKEN = '?access_token=';
	const REFERENCE_FILTER = '_eq_reference_id';
	const ZOOM_PREFIX = 'Zoom_';
	const ZOOM_LOCK_TTL = 120;
	const ZOOM_TRANSCRIPT_FILE_TYPE = 'vtt';
	const ZOOM_CHAT_FILE_TYPE = 'txt';
	const ZOOM_LABEL = 'Zoom';

	protected static $FILE_VIDEO_TYPES = array('MP4');
	protected static $FILE_CAPTION_TYPES = array('TRANSCRIPT');
	protected static $FILE_CHAT_TYPES = array('CHAT');
	protected $zoomConfiguration;
	protected $zoomClient;

	/**
	 * kZoomEngine constructor.
	 * @param $zoomConfiguration
	 */
	public function __construct($zoomConfiguration)
	{
		$this->zoomConfiguration = $zoomConfiguration;
		$this->zoomClient = new kZoomClient($zoomConfiguration[kZoomClient::ZOOM_BASE_URL]);
	}

	/**
	 * @return kZoomEvent
	 */
	public function parseEvent()
	{
		kZoomOauth::verifyHeaderToken($this->zoomConfiguration);
		$data = $this->getRequestData();
		KalturaLog::debug('Zoom event data is ' . print_r($data, true));
		$event = new kZoomEvent();
		$event->parseData($data);
		return $event;
	}

	/**
	 * @param kZoomEvent $event
	 */
	public function processEvent($event)
	{
		switch($event->eventType)
		{
			case kEventType::RECORDING_VIDEO_COMPLETED:
				$this->handleRecordingVideoComplete($event);
				break;
			case kEventType::RECORDING_TRANSCRIPT_COMPLETED:
				$this->handleRecordingTranscriptComplete($event);
				break;
		}
	}

	/**
	 * @return mixed
	 * @throws Exception
	 */
	protected function getRequestData()
	{
		$request_body = file_get_contents(self::PHP_INPUT);
		$data = json_decode($request_body, true);
		return $data;
	}

	/**
	 * @param kZoomEvent $event
	 */
	protected function handleRecordingTranscriptComplete($event)
	{
		/* @var kZoomTranscriptCompleted $transcript */
		$transcript = $event->object;
		$zoomIntegration = ZoomHelper::getZoomIntegration();
		$dbUser = $this->getEntryOwner($transcript->hostEmail, $zoomIntegration);
		$this->initUserPermissions($dbUser);
		$entry = $this->getZoomEntryByMeetingId($transcript->id);
		$this->initUserPermissions($dbUser, true);
		$captionAssetService = new CaptionAssetService();
		$captionAssetService->initService('caption_captionasset', 'captionAsset', 'setContent');
		$resourceReservation = new kResourceReservation(self::ZOOM_LOCK_TTL, true);
		foreach ($transcript->recordingFiles as $recordingFile)
		{
			/* @var kZoomRecordingFile $recordingFile */

			if (!in_array ($recordingFile->fileType, self::$FILE_CAPTION_TYPES) || !$resourceReservation->reserve($recordingFile->id))
			{
				continue;
			}

			try
			{
				$captionAsset = $this->createAssetForTranscription($entry);
				$captionAssetResource = new KalturaUrlResource();
				$captionAssetResource->url = $recordingFile->download_url . self::URL_ACCESS_TOKEN . $event->downloadToken;
				$captionAssetService->setContentAction($captionAsset->getId(), $captionAssetResource);
			}
			catch (Exception $e)
			{
				ZoomHelper::exitWithError(kVendorErrorMessages::ERROR_HANDLING_TRANSCRIPT);
			}
		}
	}

	protected function getZoomEntryByMeetingId($meetingId)
	{
		$entryFilter = new entryFilter();
		$pager = new KalturaFilterPager();
		$entryFilter->setPartnerSearchScope(baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE);
		$entryFilter->set(self::REFERENCE_FILTER, self::ZOOM_PREFIX . $meetingId);
		$c = KalturaCriteria::create(entryPeer::OM_CLASS);
		$pager->attachToCriteria($c);
		$entryFilter->attachToCriteria($c);
		$c->add(entryPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_SYSTEM, Criteria::NOT_EQUAL);
		if (kEntitlementUtils::getEntitlementEnforcement() && !kCurrentContext::$is_admin_session && entryPeer::getUserContentOnly())
		{
			entryPeer::setFilterResults(true);
		}

		$entry = entryPeer::doSelectOne($c);
		if(!$entry)
		{
			ZoomHelper::exitWithError(kVendorErrorMessages::MISSING_ENTRY_FOR_ZOOM_MEETING . $meetingId);
		}

		KalturaLog::info('Found entry:' . $entry->getId());
		return $entry;
	}

	/**
	 * @param kZoomEvent $event
	 */
	protected function handleRecordingVideoComplete($event)
	{
		$zoomIntegration = ZoomHelper::getZoomIntegration();
		/* @var kZoomMeeting $meeting */
		$meeting = $event->object;

		$resourceReservation = new kResourceReservation(self::ZOOM_LOCK_TTL, true);
		if(!$resourceReservation->reserve($meeting->id, false))
		{
			return;
		}

		$dbUser = $this->getEntryOwner($meeting->hostEmail, $zoomIntegration);
		$this->initUserPermissions($dbUser);
		$participantsUsersNames = $this->extractMeetingParticipants($meeting->id, $zoomIntegration, $dbUser->getPuserId());
		$validatedUsers = $this->getValidatedUsers($participantsUsersNames, $zoomIntegration->getPartnerId(), $zoomIntegration->getCreateUserIfNotExist());
		$entry = null;
		foreach ($meeting->recordingFiles as $recordingFile)
		{
			/* @var kZoomRecordingFile $recordingFile */
			if (in_array ($recordingFile->fileType, self::$FILE_VIDEO_TYPES))
			{
				$entry = $this->handleVideoRecord($meeting, $dbUser, $zoomIntegration, $validatedUsers, $recordingFile, $event);
			}
		}

		foreach ($meeting->recordingFiles as $recordingFile)
		{
			/* @var kZoomRecordingFile $recordingFile */
			if (in_array ($recordingFile->fileType, self::$FILE_CHAT_TYPES))
			{
				$this->handleChatRecord($entry, $meeting, $recordingFile->download_url, $event->downloadToken, $dbUser);
			}
		}
	}

	/**
	 * @param entry $entry
	 * @param kZoomMeeting $meeting
	 * @param string $chatDownloadUrl
	 * @param string $downloadToken
	 * @param kuser $dbUser
	 */
	protected function handleChatRecord($entry, $meeting, $chatDownloadUrl, $downloadToken, $dbUser)
	{
		if(!$entry)
		{
			ZoomHelper::exitWithError(kVendorErrorMessages::MISSING_ENTRY_FOR_CHAT);
		}
		try
		{
			$attachmentAsset = $this->createAttachmentAssetForChatFile($meeting->id, $entry);
			$attachmentAssetResource = new KalturaUrlResource();
			$attachmentAssetResource->url = $chatDownloadUrl . self::URL_ACCESS_TOKEN . $downloadToken;
			$this->initUserPermissions($dbUser, true);
			$attachmentAssetService = new AttachmentAssetService();
			$attachmentAssetService->initService('attachment_attachmentasset', 'attachmentAsset', 'setContent');
			$attachmentAssetService->setContentAction($attachmentAsset->getId(), $attachmentAssetResource);
		}
		catch (Exception $e)
		{
			ZoomHelper::exitWithError(kVendorErrorMessages::ERROR_HANDLING_CHAT);
		}
	}

	protected function handleVideoRecord($meeting, $dbUser, $zoomIntegration, $validatedUsers, $recordingFile, $event)
	{
		$entry = $this->createEntryFromMeeting($meeting, $dbUser);
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

	/**
	 * @param $entryId
	 * @param $categoryId
	 * @param $partnerId
	 * @throws PropelException
	 */
	protected function createCategoryEntry($entryId, $categoryId, $partnerId)
	{
		$categoryEntry = new categoryEntry();
		$categoryEntry->setEntryId($entryId);
		$categoryEntry->setCategoryId($categoryId);
		$categoryEntry->setPartnerId($partnerId);
		$categoryEntry->setStatus(CategoryEntryStatus::ACTIVE);
		$categoryEntry->save();
	}

	protected function getValidatedUsers($usersNames, $partnerId, $createIfNotFound)
	{
		$validatedUsers=array();
		if(!$usersNames)
		{
			return $usersNames;
		}

		foreach ($usersNames as $userName)
		{
			if(kuserPeer::getKuserByPartnerAndUid($partnerId, $userName, true))
			{
				$validatedUsers[] = $userName;
			}
			elseif($createIfNotFound)
			{
				kuserPeer::createKuserForPartner($partnerId, $userName);
				$validatedUsers[] = $userName;
			}
		}

		return $validatedUsers;
	}

	/**
	 * @param kZoomMeeting $meeting
	 * @return string
	 */
	protected function createEntryDescriptionFromMeeting($meeting)
	{
		return "Zoom Recording ID: {$meeting->id}\nMeeting Time: {$meeting->startTime}";
	}

	/**
	 * @param ZoomVendorIntegration $zoomIntegration
	 * @param entry $entry
	 */
	protected function setEntryCategory($zoomIntegration, $entry)
	{
		if ($zoomIntegration->getZoomCategory())
		{
			$entry->setCategories($zoomIntegration->getZoomCategory());
		}
	}

	/**
	 * @param kZoomMeeting $meeting
	 * @param kuser $owner
	 * @return entry
	 */
	protected function createEntryFromMeeting($meeting, $owner)
	{
		$entry = new entry();
		$entry->setType(entryType::MEDIA_CLIP);
		$entry->setSourceType(EntrySourceType::URL);
		$entry->setMediaType(entry::ENTRY_MEDIA_TYPE_VIDEO);
		$entry->setDescription($this->createEntryDescriptionFromMeeting($meeting));
		$entry->setName($meeting->topic);
		$entry->setPartnerId($owner->getPartnerId());
		$entry->setStatus(entryStatus::NO_CONTENT);
		$entry->setPuserId($owner->getPuserId());
		$entry->setKuserId($owner->getKuserId());
		$entry->setConversionProfileId(myPartnerUtils::getConversionProfile2ForPartner($owner->getPartnerId())->getId());
		$entry->setAdminTags(self::ADMIN_TAG_ZOOM);
		$entry->setReferenceID(self::ZOOM_PREFIX . $meeting->id);
		return $entry;
	}

	/**
	 * @param entry $entry
	 * @return CaptionAsset
	 */
	protected function createAssetForTranscription($entry)
	{
		$caption = new CaptionAsset();
		$caption->setEntryId($entry->getId());
		$caption->setPartnerId($entry->getPartnerId());
		$caption->setLanguage(KalturaLanguage::EN);
		$caption->setLabel(self::ZOOM_LABEL);
		$caption->setContainerFormat(CaptionType::WEBVTT);
		$caption->setStatus(CaptionAsset::ASSET_STATUS_QUEUED);
		$caption->setFileExt(self::ZOOM_TRANSCRIPT_FILE_TYPE);
		$caption->save();
		return $caption;
	}

	/**
	 * @param string $meetingId
	 * @return AttachmentAsset
	 */
	protected function createAttachmentAssetForChatFile($meetingId, $entry)
	{
		$attachment = new AttachmentAsset();
		$attachment->setFilename("Meeting {$meetingId} chat file." . self::ZOOM_CHAT_FILE_TYPE);
		$attachment->setPartnerId($entry->getPartnerId());
		$attachment->setEntryId($entry->getId());
		$attachment->setcontainerFormat(AttachmentType::TEXT);
		$attachment->setFileExt(self::ZOOM_CHAT_FILE_TYPE);
		$attachment->save();
		return $attachment;
	}

	/**
	 * @param $meetingId
	 * @param ZoomVendorIntegration $zoomIntegration
	 * @param $meetingOwnerName
	 * @return array participants users names
	 */
	protected function extractMeetingParticipants($meetingId, $zoomIntegration, $meetingOwnerName)
	{
		if ($zoomIntegration->getHandleParticipantsMode() == kHandleParticipantsMode::IGNORE)
		{
			return null;
		}

		$accessToken = kZoomOauth::getValidAccessToken($zoomIntegration);
		$participantsData = $this->zoomClient->retrieveMeetingParticipant($accessToken, $meetingId);
		$participants = new kZoomParticipants();
		$participants->parseData($participantsData);
		$participantsEmails = $participants->getParticipantsEmails();
		if($participantsEmails)
		{
			KalturaLog::info('Found the following participants: ' . implode(", ", $participantsEmails));
			$result = array();
			foreach ($participantsEmails as $participantEmail)
			{
				$userName = $this->matchZoomUserName($participantEmail, $zoomIntegration);
				if($meetingOwnerName != $userName)
				{
					$result[] = $userName;
				}
			}
		}
		else
		{
			$result = null;
		}

		return $result;
	}

	/**
	* @param string $hostEmail
	* @param ZoomVendorIntegration $zoomIntegration
	* @return kuser
	*/
	public function getEntryOwner($hostEmail, $zoomIntegration)
	{
		$partnerId = $zoomIntegration->getPartnerId();
		$hostEmail = $this->matchZoomUserName($hostEmail, $zoomIntegration);
		$dbUser = kuserPeer::getKuserByPartnerAndUid($partnerId, $hostEmail, true);
		if (!$dbUser)
		{
			if ($zoomIntegration->getCreateUserIfNotExist())
			{
				$dbUser = kuserPeer::createKuserForPartner($partnerId, $hostEmail);
			}
			else
			{
				$dbUser = kuserPeer::getKuserByPartnerAndUid($partnerId, $zoomIntegration->getDefaultUserEMail(), true);
			}
		}

		return $dbUser;
	}

	/**
	 * @param string $userName
	 * @param ZoomVendorIntegration $zoomIntegration
	 * @return string kalturaUserName
	 */
	public function matchZoomUserName($userName, $zoomIntegration)
	{
		$result = $userName;
		switch ($zoomIntegration->getUserMatching())
		{
			case kZoomUsersMatching::DO_NOT_MODIFY:
				break;
			case kZoomUsersMatching::ADD_POSTFIX:
				$postFix = $zoomIntegration->getUserPostfix();
				if (!kString::endsWith($result, $postFix))
				{
					$result = $result . $postFix;
				}

				break;
			case kZoomUsersMatching::REMOVE_POSTFIX:
				$postFix = $zoomIntegration->getUserPostfix();
				if (kString::endsWith($result, $postFix))
				{
					$result = substr($result, 0, strlen($result) - strlen($postFix));
				}

				break;
		}

		return $result;
	}

	/**
	 * user logged in - need to re-init kPermissionManager in order to determine current user's permissions
	 * @param kuser $dbUser
	 * @param bool $isAdmin
	 */
	protected function initUserPermissions($dbUser, $isAdmin = false)
	{
		$ks = null;
		kSessionUtils::createKSessionNoValidations($dbUser->getPartnerId(), $dbUser->getPuserId() , $ks, 86400 , $isAdmin , "" , '*' );
		KalturaLog::info('changing to ks: ' . $ks);
		kCurrentContext::initKsPartnerUser($ks);
		kPermissionManager::init();
	}
}