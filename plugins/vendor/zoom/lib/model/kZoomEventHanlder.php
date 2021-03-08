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
		$zoomDropFolderId = self::getZoomDropFolderId($event);
		switch($event->eventType)
		{
			case kEventType::RECORDING_VIDEO_COMPLETED:
			case kEventType::RECORDING_TRANSCRIPT_COMPLETED:
				KalturaLog::notice('This is an old Zoom event type - Not processing');
				break;
			case kEventType::NEW_RECORDING_VIDEO_COMPLETED:
				if ($zoomDropFolderId)
				{
					self::createZoomDropFolderFile($event, $zoomDropFolderId);
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
					self::createZoomDropFolderFile($event, $zoomDropFolderId);
				}
				else
				{
					$transcriptProcessor = new kZoomTranscriptProcessor($this->zoomConfiguration[kZoomClient::ZOOM_BASE_URL]);
					$transcriptProcessor->handleRecordingTranscriptComplete($event);
				}
				break;
		}
	}
	
	protected static function getZoomDropFolderId(kZoomEvent $event)
	{
		$zoomVendorIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartner($event->accountId, VendorTypeEnum::ZOOM_ACCOUNT);
		$dropFolderType = ZoomDropFolderPlugin::getDropFolderTypeCoreValue(ZoomDropFolderType::ZOOM);
		$dropFolders = DropFolderPeer::retrieveEnabledDropFoldersPerPartner($zoomVendorIntegration->getPartnerId(), $dropFolderType);
		foreach ($dropFolders as $dropFolder)
		{
			if ($dropFolder->zoomVendorIntegrationId == $zoomVendorIntegration->getId())
			{
				return $zoomVendorIntegration->getId();
			}
		}
		return null;
	}
	
	protected static function createZoomDropFolderFile(kZoomEvent $event, $dropFolderId)
	{
		/* @var kZoomRecording $recording */
		$recording = $event->object;
		
		$dropFolderFilesMap = self::loadDropFolderFiles($dropFolderId);
		
		$kMeetingMetaData = new kalturaMeetingMetadata();
		$kMeetingMetaData->meetingId = $recording->id;
		$kMeetingMetaData->uuid = $recording->uuid;
		$kMeetingMetaData->topic = $recording->topic;
		$kMeetingMetaData->meetingStartTime = $recording->startTime;
		$kMeetingMetaData->accountId = $event->accountId;
		$kMeetingMetaData->hostId = $recording->hostId;
		$kMeetingMetaData->type = $recording->recordingType;
		
		/* @var kZoomRecordingFile $recordingFile*/
		foreach ($recording->recordingFiles as $recordingFile)
		{
			$fileName = $kMeetingMetaData->uuid . '_' . $recordingFile->id . ZoomHelper::SUFFIX_ZOOM;
			if(!array_key_exists($fileName, $dropFolderFilesMap) &&
				ZoomHelper::shouldHandleFileType($recordingFile->recordingFileType))
			{
				$kRecordingFile = new kalturaRecordingFile();
				$kRecordingFile->id = $recordingFile->id;
				$kRecordingFile->downloadUrl = $recordingFile->download_url;
				$kRecordingFile->fileType = $recordingFile->recordingFileType;
				$kRecordingFile->recordingStart = $recordingFile->recordingStart;
				
				$zoomDropFolderFile = new ZoomDropFolderFile();
				$zoomDropFolderFile->setDropFolderId($dropFolderId);
				$zoomDropFolderFile->setFileName($fileName);
				$zoomDropFolderFile->setFileSize($recordingFile->fileSize);
				$zoomDropFolderFile->setMeetingMetadata($kMeetingMetaData);
				$zoomDropFolderFile->setRecordingFile($kRecordingFile);
				$zoomDropFolderFile->save();
			}
			else
			{
				KalturaLog::notice('Drop folder file already existed: ' . print_r($dropFolderFilesMap[$fileName], true));
			}
		}
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