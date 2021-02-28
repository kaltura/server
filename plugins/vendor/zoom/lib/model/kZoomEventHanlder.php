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
		switch($event->eventType)
		{
			case kEventType::RECORDING_VIDEO_COMPLETED:
			case kEventType::RECORDING_TRANSCRIPT_COMPLETED:
				KalturaLog::notice('This is an old Zoom event type - Not processing');
				break;
			case kEventType::NEW_RECORDING_VIDEO_COMPLETED:
				self::createZoomDropFolderFile($event);
				break;
			case kEventType::NEW_RECORDING_TRANSCRIPT_COMPLETED:
				self::createZoomDropFolderFile($event);
				break;
		}
	}
	
	protected static function createZoomDropFolderFile(kZoomEvent $event)
	{
		/* @var kZoomRecording $recording */
		$recording = $event->object;
		
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
			$kRecordingFile = new kalturaRecordingFile();
			$kRecordingFile->id = $recordingFile->id;
			$kRecordingFile->downloadUrl = $recordingFile->download_url;
			$kRecordingFile->fileType = $recordingFile->recordingFileType;
			$kRecordingFile->recordingStart = $recordingFile->recordingStart;
			
			$zoomDropFolderFile = new ZoomDropFolderFile();
			$zoomDropFolderFile->setMeetingMetadata($kMeetingMetaData);
			$zoomDropFolderFile->setRecordingFile($kRecordingFile);
			$zoomDropFolderFile->save();
		}
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