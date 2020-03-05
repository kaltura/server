<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.data
 */
class kZoomRecording implements iZoomObject
{
	const HOST_EMAIL = 'host_email';
	const RECORDING_FILES = 'recording_files';
	const RECORDING_ID = 'id';
	const RECORDING_UUID = 'uuid';
	const TOPIC = 'topic';
	const MEETING_OBJECT = 'meeting';
	const OBJECT = 'object';
	const START_TIME = 'start_time';
	const TYPE = 'type';

	public $id;
	public $uuid;
	public $topic;
	public $hostEmail;
	public $recordingFiles;
	public $startTime;

	/**
	 * @var kRecordingType
	 */
	public $recordingType;

	public function parseData($data)
	{
		$this->hostEmail = $data[self::HOST_EMAIL];
		$this->id = $data[self::RECORDING_ID];
		$this->uuid = $data[self::RECORDING_UUID];
		$this->topic = $data[self::TOPIC];
		$this->startTime = $data[self::START_TIME];
		$this->parseType( $data[self::TYPE]);
		$this->parseRecordingFiles($data[self::RECORDING_FILES]);
	}

	public function parseRecordingFiles($recordingFilesData)
	{
		$this->recordingFiles = array();
		foreach($recordingFilesData as $recordFileData)
		{
			$kZoomRecordingFile = new kZoomRecordingFile();
			$kZoomRecordingFile->parseData($recordFileData);
			if(!isset($this->recordingFiles[$kZoomRecordingFile->recordingFileType]))
			{
				$this->recordingFiles[$kZoomRecordingFile->recordingFileType] = array();
			}

			$this->recordingFiles[$kZoomRecordingFile->recordingFileType][] = $kZoomRecordingFile;
		}
	}

	protected function parseType($recordingType)
	{
		/*
		 * If the recording is of a meeting, the type can be one of the following Meeting types:
		 * 1 - Instant meeting
		 * 2 - Scheduled meeting
		 * 3 - Recurring meeting with no fixed time
		 * 8 - Recurring meeting with fixed time
		 *
		 * If the recording is of a Webinar, the type can be one of the following Webinar Types:
		 * 5 - Webinar
		 * 6 - Recurring Webinar without a fixed time
		 * 9 - Recurring Webinar with a fixed time
		 */
		switch($recordingType)
		{
			case 1:
			case 2:
			case 3:
			case 8:
				$this->recordingType = kRecordingType::MEETING;
				break;
			case 5:
			case 6:
			case 9:
				$this->recordingType = kRecordingType::WEBINAR;
				break;
			default:
				$this->recordingType = kRecordingType::MEETING;
		}
	}
}
