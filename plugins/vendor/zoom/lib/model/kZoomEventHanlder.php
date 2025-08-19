<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.model
 */

class kZoomEventHanlder
{
	protected $zoomConfiguration;
	const EMAIL = 'email';
	const CMS_USER_FIELD = 'cms_user_id';
	const KALTURA_ZOOM_DEFAULT_USER = 'KalturaZoomDefault';

	/**
	 * kZoomEngine constructor.
	 * @param $zoomConfiguration
	 */
	public function __construct($zoomConfiguration)
	{
		$this->zoomConfiguration = $zoomConfiguration;
	}
	
	/**
	 * @return kZoomEvent
	 * @param array $data
	 * @throws Exception
	 */
	public function parseEvent($data)
	{
		kZoomOauth::verifyHeaderToken($this->zoomConfiguration);
		KalturaLog::debug('Zoom event data is ' . print_r($data, true));
		$event = new kZoomEvent();
		$event->parseData($data);
		return $event;
	}

	public function processUrlValidationEvent($data)
	{
		$response = new KalturaEndpointValidationResponse();
		$payload = $data[kZoomEvent::PAYLOAD];
		KalturaLog::debug("Zoom endpoint validation payload: " . print_r($payload, true));
		$response->plainToken = $payload[kZoomEvent::PLAIN_TOKEN];
		$secretToken = $this->zoomConfiguration[kOAuth::SECRET_TOKEN];
		$response->encryptedToken = hash_hmac("sha256", $response->plainToken, $secretToken);
		return $response;
	}

	/**
	 * @param kZoomEvent $event
	 * @throws KalturaAPIException
	 * @throws kCoreException
	 * @throws PropelException
	 */
	public function processEvent($event)
	{
		/* @var ZoomVendorIntegration $zoomVendorIntegration */
		$zoomVendorIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartner($event->accountId, VendorTypeEnum::ZOOM_ACCOUNT);
		$zoomDropFolder = self::getZoomDropFolder($zoomVendorIntegration);
		$zoomDropFolderId =  $zoomDropFolder ? $zoomDropFolder->getId() : null;
		$zoomClient = $this->initZoomClient($zoomVendorIntegration);
		if (self::shouldExcludeUserFromSavingRecording($event, $zoomClient, $zoomVendorIntegration))
		{
			return;
		}
		switch($event->eventType)
		{
			case kEventType::RECORDING_VIDEO_COMPLETED:
			case kEventType::RECORDING_TRANSCRIPT_COMPLETED:
				KalturaLog::notice('This is an old Zoom event type - Not processing');
				break;
			case kEventType::NEW_RECORDING_VIDEO_COMPLETED:
				if ($zoomDropFolderId)
				{
					self::createZoomDropFolderFile($event, $zoomDropFolderId, $zoomVendorIntegration->getPartnerId(), $zoomVendorIntegration,
					                               $zoomDropFolder->getConversionProfileId(), $zoomDropFolder->getFileDeletePolicy());
				}
				else
				{
					/* @var kZoomRecording $recording */
					$recording = $event->object;
					$zoomBaseUrl = $this->zoomConfiguration[kZoomClient::ZOOM_BASE_URL];
					if($recording->recordingType == kRecordingType::WEBINAR)
					{
						$zoomRecordingProcessor = new kZoomWebinarProcessor($zoomBaseUrl);
					}
					else
					{
						$zoomRecordingProcessor = new kZoomMeetingProcessor($zoomBaseUrl);
					}
					
					$zoomRecordingProcessor->handleRecordingVideoComplete($event);
				}
				break;
			case kEventType::NEW_RECORDING_TRANSCRIPT_COMPLETED:
				if ($zoomDropFolderId)
				{
					self::createZoomDropFolderFile($event, $zoomDropFolderId, $zoomVendorIntegration->getPartnerId(), $zoomVendorIntegration, $zoomDropFolder->getConversionProfileId());
				}
				else
				{
					$transcriptProcessor = new kZoomTranscriptProcessor($this->zoomConfiguration[kZoomClient::ZOOM_BASE_URL], $zoomVendorIntegration->getAccountId(),
												$zoomVendorIntegration->getRefreshToken(), $zoomVendorIntegration->getAccessToken(),
												$zoomVendorIntegration->getExpiresIn(), $zoomVendorIntegration->getZoomAuthType());
					$transcriptProcessor->handleRecordingTranscriptComplete($event);
				}
				break;
		}
	}
	
	protected static function shouldExcludeUserFromSavingRecording ($event, $zoomClient, ZoomVendorIntegration $zoomVendorIntegration)
	{
		if ($zoomVendorIntegration->getGroupParticipationType() == kZoomGroupParticipationType::NO_CLASSIFICATION)
		{
			KalturaLog::debug('Account is not configured to OPT IN or OPT OUT');
			return false;
		}
		/* @var kZoomRecording $object*/
		$eventObject = $event->object;
		$hostEmail = $eventObject->hostEmail;
		$userId = self::getEntryOwnerId($hostEmail, $zoomVendorIntegration->getPartnerId(), $zoomVendorIntegration, $zoomClient);
		if ($zoomVendorIntegration->shouldExcludeUserRecordingsIngest($userId))
		{
			KalturaLog::notice('The user ['. $userId .'] is configured to not save recordings - Not processing');
			return true;
		}
		return false;
	}

	public static function getKuserExternalId($externalId)
	{
		$userSearch = new kUserSearch();

		$userItem = new ESearchUserItem();
		$userItem->setFieldName(ESearchUserFieldName::EXTERNAL_ID);
		$userItem->setItemType(ESearchItemType::EXACT_MATCH);
		$userItem->setSearchTerm($externalId);

		$operator = new ESearchOperator();
		$operator->setOperator(ESearchOperatorType::AND_OP);
		$operator->setSearchItems(array($userItem));

		$pager = new kPager();
		$pager->setPageSize(1);

		$result = $userSearch->doSearch($operator, $pager);
		$rawResult = kESearchCoreAdapter::transformElasticToCoreObject($result, $userSearch);
		if (is_array($rawResult[0]) && isset($rawResult[0][0]) && !is_null($rawResult[0][0]))
		{
			$user = $rawResult[0][0]->getObject();
			KalturaLog::debug('Found user with external id [' . $user->getExternalId() . ']');
			return $user;
		}
		return null;
	}

	protected static function getEntryOwnerId($hostEmail, $partnerId, $zoomVendorIntegration, $zoomClient)
	{
		/* @var ZoomVendorIntegration $zoomVendorIntegration */
		$userId = self::KALTURA_ZOOM_DEFAULT_USER;
		if($hostEmail == '')
		{
			return $zoomVendorIntegration->getCreateUserIfNotExist() ? $userId : $zoomVendorIntegration->getDefaultUserEMail();
		}
		$puserId = self::processZoomUserName($hostEmail, $zoomVendorIntegration, $zoomClient);

		KalturaLog::debug('Finding Zoom user name: ' . $puserId);
		$user = kuserPeer::getKuserByPartnerAndUid($partnerId, $puserId);
		if (!$user)
		{
			switch ($zoomVendorIntegration->getUserSearchMethod())
			{
				case kZoomUsersSearchMethod::EXTERNAL:
				{
					KalturaLog::debug('Could not find by id. Searching by external_id');
					$user = self::getKuserExternalId($puserId);
					break;
				}
				case kZoomUsersSearchMethod::EMAIL:
				default:
				{
					KalturaLog::debug('Could not find by id. Searching by email');
					$user = kuserPeer::getKuserByEmail($hostEmail, $partnerId);
					break;
				}
			}
		}

		if (!$user)
		{
			if ($zoomVendorIntegration->getCreateUserIfNotExist())
			{
				$userId = $puserId;
				KalturaLog::debug('User not found. Creating new user with id [' . $userId . ']');
			}
			else if ($zoomVendorIntegration->getDefaultUserEMail())
			{
				$userId = $zoomVendorIntegration->getDefaultUserEMail();
				KalturaLog::debug('User not found. Returning default with id [' . $userId . ']');
			}
		}
		else
		{
			$userId = $user->getPuserId();
			KalturaLog::debug('Found user with id [' . $userId . ']');
		}

		return $userId;
	}
	
	public static function processZoomUserName($userName, $zoomVendorIntegration, $zoomClient)
	{
		/* @var ZoomVendorIntegration $zoomVendorIntegration */
		$result = $userName;
		switch ($zoomVendorIntegration->getUserMatching())
		{
			case kZoomUsersMatching::ADD_POSTFIX:
				$postFix = $zoomVendorIntegration->getUserPostfix();
				if (!kString::endsWith($result, $postFix, false))
				{
					$result = $result . $postFix;
				}
				
				break;
			case kZoomUsersMatching::REMOVE_POSTFIX:
				$postFix = $zoomVendorIntegration->getUserPostfix();
				if (kString::endsWith($result, $postFix, false))
				{
					$result = substr($result, 0, strlen($result) - strlen($postFix));
				}
				
				break;
			case kZoomUsersMatching::CMS_MATCHING:
				$zoomUser = $zoomClient->retrieveZoomUser($userName);
				if(isset($zoomUser[self::CMS_USER_FIELD]) && !empty($zoomUser[self::CMS_USER_FIELD]))
				{
					$result = $zoomUser[self::CMS_USER_FIELD];
				}
				else
				{
					KalturaLog::warning("Zoom user [{$userName}] was not matched with CMS. Owner id will be determined by the policy set in the Integration Settings");
				}
				break;
			case kZoomUsersMatching::DO_NOT_MODIFY:
			default:
				break;
		}
		
		return $result;
	}
	
	protected function initZoomClient(ZoomVendorIntegration $zoomVendorIntegration)
	{
		$accountId = $zoomVendorIntegration->getAccountId();
		$refreshToken = $zoomVendorIntegration->getRefreshToken();
		$accessToken = $zoomVendorIntegration->getAccessToken();
		$accessExpiresIn = $zoomVendorIntegration->getExpiresIn();
		$zoomAuthType = $zoomVendorIntegration->getZoomAuthType();
		$zoomConfiguration = kConf::get(ZoomHelper::ZOOM_ACCOUNT_PARAM, ZoomHelper::VENDOR_MAP);
		$zoomBaseURL = $zoomConfiguration['ZoomBaseUrl'];
		return new kZoomClient($zoomBaseURL, $accountId, $refreshToken, $accessToken, $accessExpiresIn, $zoomAuthType);
	}
	
	
	protected static function getZoomDropFolder($zoomVendorIntegration)
	{
		$dropFolderType = ZoomDropFolderPlugin::getDropFolderTypeCoreValue(ZoomDropFolderType::ZOOM);
		$dropFolders = DropFolderPeer::retrieveEnabledDropFoldersPerPartner($zoomVendorIntegration->getPartnerId(), $dropFolderType);
		foreach ($dropFolders as $dropFolder)
		{
			if ($dropFolder->getZoomVendorIntegrationId() == $zoomVendorIntegration->getId())
			{
				return $dropFolder;
			}
		}
		return null;
	}
	
	protected static function createZoomDropFolderFile(kZoomEvent $event, $dropFolderId, $partnerId, ZoomVendorIntegration $zoomVendorIntegration,
	                                                   $conversionProfileId, $fileDeletionPolicy = null)
	{
		/* @var kZoomRecording $recording */
		$recording = $event->object;
		if ($recording->recordingType == kRecordingType::WEBINAR && !$zoomVendorIntegration->getEnableWebinarUploads())
        	{
            		KalturaLog::debug('webinar uploads is disabled for vendor integration id: ' . $zoomVendorIntegration->getId());
            		return;
        	}
        	if($recording->recordingType == kRecordingType::MEETING && !$zoomVendorIntegration->getEnableMeetingUpload())
        	{
            		KalturaLog::debug('meeting uploads is disabled for vendor integration id: ' . $zoomVendorIntegration->getId());
            		return;
        	}
		$kMeetingMetaData = self::allocateMeetingMetaData($recording, $event);
		KalturaLog::debug('meeting recording files are: ' . print_r($recording->recordingFiles, true));
		$recordingFilesOrdered = $recording->orderRecordingFiles($recording->recordingFiles);
		KalturaLog::debug('recording files ordered are: ' . print_r($recordingFilesOrdered, true));
		foreach ($recordingFilesOrdered as $recordingFilesPerTimeSlot)
		{
			$parentEntry = null;
			self::handleAudioFiles($recordingFilesPerTimeSlot);
			/* @var kZoomRecordingFile $recordingFile*/
			foreach ($recordingFilesPerTimeSlot as $recordingFile)
			{
				$fileName = $kMeetingMetaData->getUuid() . '_' . $recordingFile->id . ZoomHelper::SUFFIX_ZOOM;
				$dropFolderFilesMap = self::loadDropFolderFiles($dropFolderId, $fileName);
				if(!array_key_exists($fileName, $dropFolderFilesMap))
				{
					KalturaLog::debug('No recording named: ' . $fileName);
					if(!ZoomHelper::shouldHandleFileTypeEnum($recordingFile->recordingFileType) ||
						($recordingFile->recordingFileType == kRecordingFileType::TRANSCRIPT && $zoomVendorIntegration->getEnableZoomTranscription() == 0))
					{
						continue;
					}
					$kRecordingFile = self::allocateZoomRecordingFile($recordingFile, $event);
					$zoomDropFolderFile = self::allocateZoomDropFolderFile($dropFolderId, $partnerId, $fileName, $recordingFile->fileSize,
					                                                      $kMeetingMetaData, $kRecordingFile);
					if (!$parentEntry || ($recordingFile->recordingFileType == kRecordingFileType::TRANSCRIPT))
					{
						$parentEntry = self::getEntryByReferenceId(zoomProcessor::ZOOM_PREFIX . $kMeetingMetaData->getUuid(). $recordingFile->recordingStart , $partnerId);
						if ($parentEntry)
						{
							$zoomDropFolderFile->setIsParentEntry(false);
						}
						else if($recordingFile->recordingFileType != kRecordingFileType::TRANSCRIPT)
						{
							$parentEntry = self::createEntry($recording->uuid, $partnerId, $zoomVendorIntegration->getEnableZoomTranscription(),
							                                 $recordingFile->recordingStart, $conversionProfileId);
							$zoomDropFolderFile->setIsParentEntry(true);
						}
					}
					else
					{
						$zoomDropFolderFile->setIsParentEntry(false);
					}
					
					$zoomDropFolderFile->setParentEntryId($parentEntry->getId());
					$zoomDropFolderFile->save();
					$zoomDropFolderFile->setStatus(DropFolderFileStatus::PENDING);
					$zoomDropFolderFile->save();
					kEventsManager::flushEvents();
				}
				else
				{
					KalturaLog::notice('Drop folder file already existed: ' . print_r($dropFolderFilesMap[$fileName], true));
				}
			}
		}
	}

	protected static function handleAudioFiles(&$recordingFilesPerTimeSlot)
	{
		$foundMP4 = false;
		$audioKeys = array();
		foreach ($recordingFilesPerTimeSlot as $key => $recordingFile)
		{
			if ($recordingFile->recordingFileType == kRecordingFileType::VIDEO)
			{
				$foundMP4 = true;
			}
			if ($recordingFile->recordingFileType == kRecordingFileType::AUDIO)
			{
				$audioKeys[] = $key;
			}
		}
		if ($foundMP4)
		{
			foreach ($audioKeys as $audioKey)
			{
				$audioRecordingFile = $recordingFilesPerTimeSlot[$audioKey];
				KalturaLog::debug('Video and Audio files were found. audio file is ' . print_r($audioRecordingFile, true) . ' ,unsetting Audio');
				unset($recordingFilesPerTimeSlot[$audioKey]);
			}
		}
	}
	
	protected static function allocateMeetingMetaData($recording, $event)
	{
		$kMeetingMetaData = new ZoomMeetingMetadata();
		$kMeetingMetaData->setMeetingId($recording->id);
		$kMeetingMetaData->setUuid($recording->uuid);
		$kMeetingMetaData->setTopic($recording->topic);
		$kMeetingMetaData->setMeetingStartTime(kTimeZoneUtils::strToZuluTime($recording->startTime));
		$kMeetingMetaData->setAccountId($event->accountId);
		$kMeetingMetaData->setHostId($recording->hostId);
		if (isset($recording->recordingType))
		{
			$kMeetingMetaData->setType($recording->recordingType);
		}
		return $kMeetingMetaData;
	}
	
	protected static function allocateZoomRecordingFile($recordingFile, $event)
	{
		$kRecordingFile = new ZoomRecordingFile();
		$kRecordingFile->setId($recordingFile->id);
		$kRecordingFile->setDownloadUrl($recordingFile->download_url);
		$kRecordingFile->setFileType($recordingFile->recordingFileType);
		$kRecordingFile->setRecordingStart(strtotime(str_replace(array('T','Z'),array(' ',''), $recordingFile->recordingStart)));
		$kRecordingFile->setFileExtension($recordingFile->fileExtension);
		$kRecordingFile->setDownloadToken($event->downloadToken);
		return $kRecordingFile;
	}
	
	protected static function allocateZoomDropFolderFile($dropFolderId, $partnerId, $fileName, $recordingFileSize, $kMeetingMetaData, $kRecordingFile)
	{
		$zoomDropFolderFile = new ZoomDropFolderFile();
		$zoomDropFolderFile->setDropFolderId($dropFolderId);
		$zoomDropFolderFile->setPartnerId($partnerId);
		$zoomDropFolderFile->setType(ZoomDropFolderPlugin::getDropFolderTypeCoreValue(ZoomDropFolderType::ZOOM));
		$zoomDropFolderFile->setFileName($fileName);
		$zoomDropFolderFile->setFileSize($recordingFileSize);
		$zoomDropFolderFile->setMeetingMetadata($kMeetingMetaData);
		$zoomDropFolderFile->setRecordingFile($kRecordingFile);
		$zoomDropFolderFile->setStatus(DropFolderFileStatus::UPLOADING);
		return $zoomDropFolderFile;
	}
	
	protected static function getEntryByReferenceId($referenceId, $partnerId)
	{
		KalturaLog::debug('searching entry');
		kCurrentContext::$partner_id = $partnerId;
		$entryFilter = new entryFilter();
		$entryFilter->setPartnerIdEquel($partnerId);
		$entryFilter->setPartnerSearchScope(baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE);
		$entryFilter->set('_eq_reference_id', $referenceId);
		$c = KalturaCriteria::create(entryPeer::OM_CLASS);
		$entryFilter->attachToCriteria($c);
		$pager = new KalturaFilterPager();
		$pager->attachToCriteria($c);
		$c->add(entryPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_SYSTEM, Criteria::NOT_EQUAL);
		$c->add(entryPeer::UPDATED_AT, time() - (dateUtils::HOUR * 3), Criteria::GREATER_EQUAL);
		$entryStatuses =  array(entryStatus::DELETED . ',' . entryStatus::ERROR_CONVERTING . ',' . entryStatus::ERROR_IMPORTING);
		$c->add(entryPeer::STATUS, $entryStatuses, Criteria::NOT_IN);
		$entry = entryPeer::doSelectOne($c);
		if($entry)
		{
			KalturaLog::debug('Found entry:' . $entry->getId());
		}
		return $entry;
	}
	
	protected static function createEntry($uuid, $partnerId, $enableTranscriptionViaZoom, $recordingStartTime, $conversionProfileId)
	{
		$templateEntry = null;
		$conversionProfile = conversionProfile2Peer::retrieveByPK($conversionProfileId);
		$defaultEntryId = $conversionProfile->getDefaultEntryId();
		if($defaultEntryId)
		{
			$templateEntry = entryPeer::retrieveByPKNoFilter($defaultEntryId, null, false);
		}
		
		$newEntry = new entry();
		if($templateEntry)
		{
			$newEntry->copyTemplate($templateEntry, true);
		}

		$newEntry->setType(entryType::MEDIA_CLIP);
		$newEntry->setSourceType(EntrySourceType::URL);
		$newEntry->setMediaType(entry::ENTRY_MEDIA_TYPE_VIDEO);
		$newEntry->setReferenceId(zoomProcessor::ZOOM_PREFIX . $uuid. $recordingStartTime);
		$newEntry->setStatus(entryStatus::NO_CONTENT);
		$newEntry->setPartnerId($partnerId);
		$newEntry->setBlockAutoTranscript($enableTranscriptionViaZoom);
		$newEntry->setConversionProfileId($conversionProfileId);
		$newEntry->save();
		return $newEntry;
	}
	
	protected static function loadDropFolderFiles($dropFolderId, $fileName)
	{
		$statuses = array(KalturaDropFolderFileStatus::PARSED.','.KalturaDropFolderFileStatus::DETECTED);
		$dropFolderFiles = self::retrieveByFolderIdAndStatusesNotInAndName($dropFolderId, $statuses, $fileName);
		$dropFolderFilesMap = array();
		foreach ($dropFolderFiles as $dropFolderFile)
		{
			$dropFolderFilesMap[$dropFolderFile->getFileName()] = $dropFolderFile;
		}
		return $dropFolderFilesMap;
	}
	
	protected static function retrieveByFolderIdAndStatusesNotInAndName($dropFolderId, $statuses, $fileName)
	{
		$c = new Criteria();
		$c->addAnd(DropFolderFilePeer::DROP_FOLDER_ID, $dropFolderId, Criteria::EQUAL);
		$c->addAnd(DropFolderFilePeer::STATUS, $statuses, Criteria::NOT_IN);
		$c->addAnd(DropFolderFilePeer::FILE_NAME, $fileName, Criteria::EQUAL);
		$dropFolderFiles = DropFolderFilePeer::doSelect($c);
		return $dropFolderFiles;
	}

}