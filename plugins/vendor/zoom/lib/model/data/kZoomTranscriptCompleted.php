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

	public $id;
	public $uuid;
	public $topic;
	public $recordingFiles;
	public $startTime;
	public $hostEmail;

	public function parseData($data)
	{
		$this->id = $data[self::ID];
		$this->uuid = $data[self::UUID];
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
			if(!isset($this->recordingFiles[$kZoomRecordingFile->recordingFileType]))
			{
				$this->recordingFiles[$kZoomRecordingFile->recordingFileType] = array();
			}

			$this->recordingFiles[$kZoomRecordingFile->recordingFileType][] = $kZoomRecordingFile;
		}
	}
}
