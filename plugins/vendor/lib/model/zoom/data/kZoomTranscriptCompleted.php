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

	public $id;
	public $topic;
	public $recordingFiles;
	public $startTime;

	public function parseData($data)
	{
		$this->id = $data[self::ID];
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
