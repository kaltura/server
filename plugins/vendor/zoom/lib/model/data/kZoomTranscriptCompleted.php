<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.data
 */
class kZoomTranscriptCompleted implements iZoomObject
{
	const TRANSCRIPT_OBJECT = 'meeting';
	const OBJECT = 'object';
	const RECORDING_FILES = 'recording_files';
	const ID = 'id';
	const UUID = 'uuid';
	const TOPIC = 'topic';
	const START_TIME = 'start_time';
	const HOST_EMAIL = 'host_email';
	const HOST_ID = 'host_id';
	const TYPE = 'type';
	
	public $id;
	public $uuid;
	public $topic;
	public $recordingFiles;
	public $startTime;
	public $hostEmail;
	public $hostId;
	public $recordingType;

	public function parseData($data)
	{
		$this->id = $data[self::ID];
		$this->uuid = $data[self::UUID];
		$this->topic = $data[self::TOPIC];
		$this->startTime = $data[self::START_TIME];
		$this->parseRecordingFiles($data[self::RECORDING_FILES]);
		$this->parseType( $data[self::TYPE]);
		$this->hostEmail = $data[self::HOST_EMAIL];
		$this->hostId = $data[self::HOST_ID];
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
	
	public function orderRecordingFiles($recordingFiles)
	{
		foreach($recordingFiles as $time => $recordingFileByType)
		{
			$filesOrderByRecordingType = array();
			foreach ($recordingFileByType as $recordingFile)
			{
				if(!isset($filesOrderByRecordingType[$recordingFile->recordingType]))
				{
					$filesOrderByRecordingType[$recordingFile->recordingType] = array();
				}
				$filesOrderByRecordingType[$recordingFile->recordingType][] = $recordingFile;
			}
			$recordingFiles[$time] = ZoomHelper::sortArrayByValuesArray($filesOrderByRecordingType, ZoomHelper::ORDER_RECORDING_TYPE);
		}
		return $recordingFiles;
	}

	//TODO there are way to similarities between kZoomTranscriptCompleted and kZoomRecording need to make sure this is handled
	public function parseType($recordingType)
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
