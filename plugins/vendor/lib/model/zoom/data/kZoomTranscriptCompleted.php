<?php
/**
 * @package plugins.venodr
 * @subpackage zoom.data
 */
class kZoomTranscriptCompleted implements iZoomObject
{
	const TRANSCRIPT_OBJECT = 'meeting';
	const RECORDING_FILES = 'recording_files';
	const ID = 'id';
	const TOPIC = 'topic';
	const START_TIME = 'start_time';
	const HOST_EMAIL = 'host_email';

	public $id;
	public $topic;
	public $recordingFiles;
	public $startTime;
	public $hostEmail;

	public function parseData($data)
	{
		$this->id = $data[self::ID];
		$this->topic = $data[self::TOPIC];
		$this->startTime = $data[self::START_TIME];
		$this->parseRecordingFiles($data[self::RECORDING_FILES]);
		$this->hostEmail = $data[self::HOST_EMAIL];
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
