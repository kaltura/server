<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.model
 */

class kZoomEventHanlder
{
	const PHP_INPUT = 'php://input';
	protected $zoomConfiguration;
	const CONFIGURATION_PARAM_NAME = 'ZoomAccount';
	const MAP_NAME = 'vendor';

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
	 * @throws Exception
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
	 * @throws KalturaAPIException
	 * @throws kCoreException
	 * @throws PropelException
	 */
	public function processEvent($event)
	{
		$zoomVendorIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartner($event->accountId, VendorTypeEnum::ZOOM_ACCOUNT);
		$zoomDropFolder = self::getZoomDropFolder($zoomVendorIntegration);
		$zoomDropFolderId =  $zoomDropFolder ? $zoomDropFolder->getId() : null;
		switch($event->eventType)
		{
			case kEventType::RECORDING_VIDEO_COMPLETED:
			case kEventType::RECORDING_TRANSCRIPT_COMPLETED:
				KalturaLog::notice('This is an old Zoom event type - Not processing');
				break;
			case kEventType::NEW_RECORDING_VIDEO_COMPLETED:
				if ($zoomDropFolderId)
				{
					$zoomClient = $this->initZoomClient($zoomVendorIntegration);
					self::createZoomDropFolderFile($event, $zoomDropFolderId, $zoomVendorIntegration->getPartnerId(), $zoomVendorIntegration,
					                               $zoomDropFolder->getConversionProfileId(), $zoomClient, $zoomDropFolder->getFileDeletePolicy());
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
					$transcriptProcessor = new kZoomTranscriptProcessor($this->zoomConfiguration[kZoomClient::ZOOM_BASE_URL],
					                                                    $zoomVendorIntegration->getJwtToken(),
					                                                    $zoomVendorIntegration->getRefreshToken(),
					                                                    null, null, $zoomVendorIntegration->getAccessToken());
					$transcriptProcessor->handleRecordingTranscriptComplete($event);
				}
				break;
		}
	}
	
	protected function initZoomClient(ZoomVendorIntegration $zoomVendorIntegration)
	{
		$jwtToken = $zoomVendorIntegration->getJwtToken();
		$refreshToken = $zoomVendorIntegration->getRefreshToken();
		$accessToken = $zoomVendorIntegration->getAccessToken();
		$zoomConfiguration = kConf::get(self::CONFIGURATION_PARAM_NAME, self::MAP_NAME);
		$clientId = $zoomConfiguration['clientId'];
		$zoomBaseURL = $zoomConfiguration['ZoomBaseUrl'];
		$clientSecret = $zoomConfiguration['clientSecret'];
		return new kZoomClient($zoomBaseURL, $jwtToken, $refreshToken, $clientId, $clientSecret, $accessToken);
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
	                                                   $conversionProfileId, kZoomClient $zoomClient = null, $fileDeletionPolicy = null)
	{
		/* @var kZoomRecording $recording */
		$recording = $event->object;
		if (($recording->recordingType == kRecordingType::WEBINAR && !$zoomVendorIntegration->getEnableWebinarUploads()) ||
			$recording->recordingType == kRecordingType::MEETING && $zoomVendorIntegration->getEnableMeetingUpload() === 0)
		{
			KalturaLog::debug('webinar uploads is disabled for vendor integration id: ' . $zoomVendorIntegration->getId());
			return;
		}
		$kMeetingMetaData = self::allocateMeetingMetaData($recording, $event);
		KalturaLog::debug('meeting recording files are: ' . print_r($recording->recordingFiles, true));
		$recordingFilesOrdered = $recording->orderRecordingFiles($recording->recordingFiles);
		KalturaLog::debug('recording files ordered are: ' . print_r($recordingFilesOrdered, true));
		foreach ($recordingFilesOrdered as $recordingFilesPerTimeSlot)
		{
			$parentEntry = null;
			self::handleAudioFiles($recordingFilesPerTimeSlot, $kMeetingMetaData->getUuid(), $zoomClient ,$fileDeletionPolicy);
			/* @var kZoomRecordingFile $recordingFile*/
			foreach ($recordingFilesPerTimeSlot as $recordingFile)
			{
				$fileName = $kMeetingMetaData->getUuid() . '_' . $recordingFile->id . ZoomHelper::SUFFIX_ZOOM;
				$dropFolderFilesMap = self::loadDropFolderFiles($dropFolderId);
				if(!array_key_exists($fileName, $dropFolderFilesMap))
				{
					if(!ZoomHelper::shouldHandleFileTypeEnum($recordingFile->recordingFileType) ||
						($recordingFile->recordingFileType == kRecordingFileType::TRANSCRIPT && $zoomVendorIntegration->getEnableZoomTranscription() === 0))
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

	protected static function handleAudioFiles(&$recordingFilesPerTimeSlot, $meetingFileUuid, kZoomClient $zoomClient, $fileDeletionPolicy)
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
		$newEntry = new entry();
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
	
	protected static function loadDropFolderFiles($dropFolderId)
	{
		$statuses = array(KalturaDropFolderFileStatus::PARSED.','.KalturaDropFolderFileStatus::DETECTED);
		$order = DropFolderFilePeer::CREATED_AT;
		$dropFolderFiles = self::retrieveByFolderIdOrderAndStatusesNotIn($dropFolderId, $order, $statuses);
		$dropFolderFilesMap = array();
		foreach ($dropFolderFiles as $dropFolderFile)
		{
			$dropFolderFilesMap[$dropFolderFile->getFileName()] = $dropFolderFile;
		}
		return $dropFolderFilesMap;
	}
	
	protected static function retrieveByFolderIdOrderAndStatusesNotIn($dropFolderId, $order, $statuses)
	{
		$c = new Criteria();
		$c->addAnd(DropFolderFilePeer::DROP_FOLDER_ID, $dropFolderId, Criteria::EQUAL);
		$c->addAnd(DropFolderFilePeer::STATUS, $statuses, Criteria::NOT_IN);
		$c->addAnd(DropFolderFilePeer::CREATED_AT, time() - dateUtils::DAY * 3, Criteria::GREATER_EQUAL);
		$c->addAscendingOrderByColumn($order);
		$dropFolderFiles = DropFolderFilePeer::doSelect($c);
		return $dropFolderFiles;
	}

	/**
	 * @return mixed
	 * @throws Exception
	 */
	protected function getRequestData()
	{
		$request_body = file_get_contents(self::PHP_INPUT);
		return json_decode($request_body, true);
	}
}