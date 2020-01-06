<?php
/**
 * @package plugins.venodr
 * @subpackage zoom.data
 */
class kZoomMeeting implements iZoomObject
{
	const HOST_EMAIL = 'host_email';
	const RECORDING_FILES = 'recording_files';
	const MEETING_ID = 'id';
	const MEETING_UUID = 'uuid';
	const TOPIC = 'topic';
	const MEETING_OBJECT = 'meeting';
	const OBJECT = 'object';
	const START_TIME = 'start_time';

	public $id;
	public $uuid;
	public $topic;
	public $hostEmail;
	public $recordingFiles;
	public $startTime;

	public function parseData($data)
	{
		$this->hostEmail = $data[self::HOST_EMAIL];
		$this->id = $data[self::MEETING_ID];
		$this->uuid = $data[self::MEETING_UUID];
		$this->topic = $data[self::TOPIC];
		$this->startTime = $data[self::START_TIME];
		$this->parseRecordingFiles($data[self::RECORDING_FILES]);
	}

	public function parseRecordingFiles($recordingFilesData)
	{
		$this->recordingFiles = array();
		foreach($recordingFilesData as $recordFileData)
		{
			$kZoomRecordingFile = new kZoomRecordingFile();
			$kZoomRecordingFile->parseData($recordFileData);
			$this->recordingFiles[] = $kZoomRecordingFile;
		}
	}
}
