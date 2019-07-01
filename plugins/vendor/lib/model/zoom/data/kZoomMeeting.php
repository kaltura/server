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
	const TOPIC = 'topic';
	const MEETING_OBJECT = 'meeting';

	public $id;
	public $topic;
	public $hostEmail;
	public $recordingFiles;
	public $start_time;

	public function parseData($data)
	{
		$this->hostEmail = $data[self::HOST_EMAIL];
		$this->id = $data[self::MEETING_ID];
		$this->topic = $data[self::TOPIC];
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
