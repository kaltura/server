<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.model
 */

class kZoomEventHanlder
{
	const PHP_INPUT = 'php://input';
	protected $zoomConfiguration;

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
		$zoomDropFolderId = self::getZoomDropFolderId($event, $zoomVendorIntegration);
		switch($event->eventType)
		{
			case kEventType::RECORDING_VIDEO_COMPLETED:
			case kEventType::RECORDING_TRANSCRIPT_COMPLETED:
				KalturaLog::notice('This is an old Zoom event type - Not processing');
				break;
			case kEventType::NEW_RECORDING_VIDEO_COMPLETED:
				if ($zoomDropFolderId)
				{
					self::createZoomDropFolderFile($event, $zoomDropFolderId, $zoomVendorIntegration->getPartnerId(),
					                               $zoomVendorIntegration->getEnableZoomTranscription());
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
					self::createZoomDropFolderFile($event, $zoomDropFolderId, $zoomVendorIntegration->getPartnerId(),
					                               $zoomVendorIntegration->getEnableZoomTranscription());
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
	
	protected static function getZoomDropFolderId(kZoomEvent $event, $zoomVendorIntegration)
	{
		$dropFolderType = ZoomDropFolderPlugin::getDropFolderTypeCoreValue(ZoomDropFolderType::ZOOM);
		$dropFolders = DropFolderPeer::retrieveEnabledDropFoldersPerPartner($zoomVendorIntegration->getPartnerId(), $dropFolderType);
		foreach ($dropFolders as $dropFolder)
		{
			if ($dropFolder->getZoomVendorIntegrationId() == $zoomVendorIntegration->getId())
			{
				return $dropFolder->getId();
			}
		}
		return null;
	}
	
	protected static function createZoomDropFolderFile(kZoomEvent $event, $dropFolderId, $partnerId, $enableZoomTranscription)
	{
		/* @var kZoomRecording $recording */
		$recording = $event->object;
		
		$dropFolderFilesMap = self::loadDropFolderFiles($dropFolderId);
		
		$kMeetingMetaData = self::allocateMeetingMetaData($recording, $event);
		$recordingFilesOrdered = $recording->orderRecordingFiles($recording->recordingFiles);
		foreach ($recordingFilesOrdered as $recordingFilesPerTimeSlot)
		{
			$parentEntry = null;
			/* @var kZoomRecordingFile $recordingFile*/
			foreach ($recordingFilesPerTimeSlot as $recordingFile)
			{
				$fileName = $kMeetingMetaData->getUuid() . '_' . $recordingFile->id . ZoomHelper::SUFFIX_ZOOM;
				if(!array_key_exists($fileName, $dropFolderFilesMap))
				{
					if(!ZoomHelper::shouldHandleFileTypeEnum($recordingFile->recordingFileType))
					{
						continue;
					}

					$kRecordingFile = self::allocateZoomRecordingFile($recordingFile, $event);
					$zoomDropFolderFile = self::allocateZoomDropFolderFile($dropFolderId, $partnerId, $fileName, $recordingFile->fileSize,
					                                                      $kMeetingMetaData, $kRecordingFile);
					if (!$parentEntry)
					{
						$parentEntry = self::getEntryByReferenceId(zoomProcessor::ZOOM_PREFIX . $kMeetingMetaData->getUuid(), $partnerId);
						if ($parentEntry)
						{
							$zoomDropFolderFile->setIsParentEntry(false);
						}
						else
						{
							$parentEntry = self::createEntry($recording->uuid, $partnerId, $enableZoomTranscription);
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
	
	protected static function allocateMeetingMetaData($recording, $event)
	{
		$kMeetingMetaData = new ZoomMeetingMetadata();
		$kMeetingMetaData->setMeetingId($recording->id);
		$kMeetingMetaData->setUuid($recording->uuid);
		$kMeetingMetaData->setTopic($recording->topic);
		$kMeetingMetaData->setMeetingStartTime(strtotime(str_replace(array('T','Z'),array(' ',''), $recording->startTime)));
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
		$entry = entryPeer::doSelectOne($c);
		if($entry)
		{
			KalturaLog::debug('Found entry:' . $entry->getId());
		}
		return $entry;
	}
	
	protected static function createEntry($uuid, $partnerId, $enableTranscriptionViaZoom)
	{
		$newEntry = new entry();
		$newEntry->setType(entryType::MEDIA_CLIP);
		$newEntry->setSourceType(EntrySourceType::URL);
		$newEntry->setMediaType(entry::ENTRY_MEDIA_TYPE_VIDEO);
		$newEntry->setReferenceId(zoomProcessor::ZOOM_PREFIX . $uuid);
		$newEntry->setStatus(entryStatus::NO_CONTENT);
		$newEntry->setPartnerId($partnerId);
		$newEntry->setBlockAutoTranscript($enableTranscriptionViaZoom);
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