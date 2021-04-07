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
		
		$kMeetingMetaData = new kMeetingMetadata();
		$kMeetingMetaData->setMeetingId($recording->id);
		$kMeetingMetaData->setUuid($recording->uuid);
		$kMeetingMetaData->setTopic($recording->topic);
		$kMeetingMetaData->setMeetingStartTime(strtotime(str_replace(array('T','Z'),array(' ',''), $recording->startTime)));
		$kMeetingMetaData->setAccountId($event->accountId);
		$kMeetingMetaData->setHostId($recording->hostId);
		$kMeetingMetaData->setType($recording->recordingType);
		
		$recordingFilesOrdered = self::orderRecordingFiles($recording->recordingFiles);
		foreach ($recordingFilesOrdered as $recordingFilesPerTimeSlot)
		{
			$firstDFFileOnTimeSlot = true;
			$isParentEntry = false;
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
					$kRecordingFile = new kRecordingFile();
					$kRecordingFile->setId($recordingFile->id);
					$kRecordingFile->setDownloadUrl($recordingFile->download_url);
					$kRecordingFile->setFileType($recordingFile->recordingFileType);
					$kRecordingFile->setRecordingStart(strtotime(str_replace(array('T','Z'),array(' ',''), $recordingFile->recordingStart)));
					$kRecordingFile->setFileExtension($recordingFile->fileExtension);
					$kRecordingFile->setDownloadToken($event->downloadToken);
					
					$zoomDropFolderFile = new ZoomDropFolderFile();
					$zoomDropFolderFile->setDropFolderId($dropFolderId);
					$zoomDropFolderFile->setPartnerId($partnerId);
					$zoomDropFolderFile->setType(ZoomDropFolderPlugin::getDropFolderTypeCoreValue(ZoomDropFolderType::ZOOM));
					$zoomDropFolderFile->setFileName($fileName);
					$zoomDropFolderFile->setFileSize($recordingFile->fileSize);
					$zoomDropFolderFile->setMeetingMetadata($kMeetingMetaData);
					$zoomDropFolderFile->setRecordingFile($kRecordingFile);
					$zoomDropFolderFile->setStatus(DropFolderFileStatus::UPLOADING);
					
					if ($firstDFFileOnTimeSlot)
					{
						$firstDFFileOnTimeSlot = false;
						$newEntry = self::createEntry($recording->uuid, $partnerId, $enableZoomTranscription);
					}
					
					$zoomDropFolderFile->setParentEntryId($newEntry->getId());
					if (!$isParentEntry && $recordingFile->recordingFileType == kRecordingFileType::VIDEO)
					{
						$isParentEntry = true;
						$zoomDropFolderFile->setIsParentEntry(true);
					}
					else
					{
						$zoomDropFolderFile->setIsParentEntry(false);
					}
					
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
	
	protected static function orderRecordingFiles($recordingFiles)
	{
		$orderedFiles = array();
		foreach($recordingFiles as $time => $recordingFileByTimeStamp)
		{
			ksort($recordingFileByTimeStamp);
			foreach ($recordingFileByTimeStamp as $recordingFileByTypes)
			{
				foreach ($recordingFileByTypes as $recordingFileByType)
				{
					$orderedFiles[] = $recordingFileByType;
				}
			}
			$recordingFiles[$time] = $orderedFiles;
		}
		return $recordingFiles;
	}
	
	protected static function loadDropFolderFiles($dropFolderId)
	{
		$statuses = KalturaDropFolderFileStatus::PARSED.','.KalturaDropFolderFileStatus::DETECTED;
		$order = DropFolderFilePeer::CREATED_AT;
		$dropFolderFiles = DropFolderFilePeer::retrieveByFolderIdOrderAndStatuses($dropFolderId, $order, $statuses);
		$dropFolderFilesMap = array();
		foreach ($dropFolderFiles as $dropFolderFile)
		{
			$dropFolderFilesMap[$dropFolderFile->fileName] = $dropFolderFile;
		}
		return $dropFolderFilesMap;
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