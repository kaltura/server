<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.data
 */
class kZoomRecordingFile implements iZoomObject
{
	const FILE_TYPE = 'file_type';
	const DOWNLOAD_URL = 'download_url';
	const ID = 'id';
	const NO_ID = 'noID';
	const RECORDING_START = 'recording_start';
	const FILE_EXTENSION = 'file_extension';
	const FILE_SIZE = 'file_size';
	const RECORDING_TYPE = 'recording_type';

	public $recordingFileType;
	public $download_url;
	public $id;
	public $recordingStart;
	public $fileExtension;
	public $fileSize;
	public $recordingType;

	public function parseData($data)
	{
		$this->parseFileType($data[self::FILE_TYPE]);
		$this->download_url = $data[self::DOWNLOAD_URL];
		$this->recordingStart = $data[self::RECORDING_START];
		$this->fileExtension = $data[self::FILE_EXTENSION];
		$this->fileSize = $data[self::FILE_SIZE];
		$this->recordingType = $data[self::RECORDING_TYPE];
		if(isset($data[self::ID]))
		{
			$this->id = $data[self::ID];
		}
		else
		{
			$this->id = self::NO_ID;
		}
	}

	public function parseFileType($fileType)
	{
		switch($fileType)
		{
			case 'MP4':
				$this->recordingFileType = kRecordingFileType::VIDEO;
			break;
			case 'CHAT':
				$this->recordingFileType = kRecordingFileType::CHAT;
				break;
			case 'TRANSCRIPT':
				$this->recordingFileType = kRecordingFileType::TRANSCRIPT;
				break;
			case 'M4A':
				$this->recordingFileType = kRecordingFileType::AUDIO;
				break;
			case 'CC':
				$this->recordingFileType = kRecordingFileType::CC;
				break;
			default:
				$this->recordingFileType = kRecordingFileType::UNDEFINED;
		}
	}
}
