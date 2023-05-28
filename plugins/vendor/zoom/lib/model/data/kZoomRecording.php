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
	const HOST_ID = 'host_id';
	const TRACKING_FIELDS = 'tracking_fields';
	const FIELD = 'field';
	const VALUE = 'value';
	const KALTURA_CATEGORY = 'KalturaCategory';
	const KALTURA_CATEGORY_PATH = 'KalturaCategoryPath';

	public $id;
	public $uuid;
	public $topic;
	public $hostEmail;
	public $recordingFiles;
	public $startTime;
	public $hostId;

	/**
	 * @var kRecordingType
	 */
	public $recordingType;

	public function parseData($data)
	{
		$this->hostEmail = $data[self::HOST_EMAIL];
		$this->hostId = $data[self::HOST_ID];
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
			if(!isset($this->recordingFiles[$kZoomRecordingFile->recordingStart]))
			{
				$this->recordingFiles[$kZoomRecordingFile->recordingStart] = array();
			}

			if(!isset($this->recordingFiles[$kZoomRecordingFile->recordingStart][$kZoomRecordingFile->recordingFileType]))
			{
				$this->recordingFiles[$kZoomRecordingFile->recordingStart][$kZoomRecordingFile->recordingFileType] = array();
			}

			$this->recordingFiles[$kZoomRecordingFile->recordingStart][$kZoomRecordingFile->recordingFileType][] = $kZoomRecordingFile;
		}
		$this->sortZoomRecordingFilesByRecordingTypes();
	}

	/**
	 * $this->recordingFiles has the following structure:
	 * [start_time][file_type][recording_files]
	 * example:
	 * [1685260915] => [video] => [file1, file2]
	 * [1685260915] => [audio] => [file1, file2]
	 * [1685261000] => [video] => [file1, file2, file3]
	 * note: order is important only for video file types
	*/
	public function sortZoomRecordingFilesByRecordingTypes()
	{
		foreach ($this->recordingFiles as $recordingStart => $fileTypeRecordingFilesArray)
		{
			if(isset($this->recordingFiles[$recordingStart][kRecordingFileType::VIDEO]))
			{
				$this->sortZoomRecordingFilesByValuesArray($this->recordingFiles[$recordingStart][kRecordingFileType::VIDEO], ZoomHelper::ORDER_RECORDING_TYPE);
			}
		}
	}

	public static function sortZoomRecordingFilesByValuesArray(&$zoomRecordingFiles, array $valuesArray)
	{
		$orderedRecordingFiles = array();
		foreach ($valuesArray as $value)
		{
			foreach ($zoomRecordingFiles as $zoomRecordingFile)
			{
				if ($zoomRecordingFile->recordingType == $value)
				{
					$orderedRecordingFiles[] = $zoomRecordingFile;
				}
			}
		}
		$zoomRecordingFiles = $orderedRecordingFiles;
	}

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
	
	public function orderRecordingFiles($recordingFiles)
	{
		foreach($recordingFiles as $time => $recordingFileByTimeStamp)
		{
			$filesOrderByRecordingType = array();
			foreach ($recordingFileByTimeStamp as $recordingFileByType)
			{
				foreach ($recordingFileByType as $recordingFile)
				{
					if(!isset($filesOrderByRecordingType[$recordingFile->recordingType]))
					{
						$filesOrderByRecordingType[$recordingFile->recordingType] = array();
					}
					$filesOrderByRecordingType[$recordingFile->recordingType][] = $recordingFile;
				}
			}
			$recordingFiles[$time] = ZoomHelper::sortArrayByValuesArray($filesOrderByRecordingType, ZoomHelper::ORDER_RECORDING_TYPE);
		}
		return $recordingFiles;
	}
}
