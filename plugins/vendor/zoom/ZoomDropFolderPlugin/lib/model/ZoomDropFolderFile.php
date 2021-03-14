<?php
/**
 * @package plugins.dropFolder
 * @subpackage model
 */
class ZoomDropFolderFile extends DropFolderFile
{
	const MEETING_METADATA = 'meetingMetadata';
	const RECORDING_FILE = 'recordingFile';
	
	/**
	 * @var kMeetingMetadata
	 */
	protected $meetingMetadata;

	/**
	 * @var kRecordingFile
	 */
	protected $recordingFile;
	
	/**
	 * return KalturaMeetingMetadata
	 */
	public function getMeetingMetadata() {return $this->getFromCustomData(self::MEETING_METADATA);}
	
	/**
	 * @param KalturaMeetingMetadata $v
	 */
	public function setMeetingMetadata ($v){$this->putInCustomData(self::MEETING_METADATA, $v);}
	
	/**
	 * return KalturaRecordingFile
	 */
	public function getRecordingFile() {return $this->getFromCustomData(self::RECORDING_FILE);}

	/**
	 * @param KalturaRecordingFile $v
	 */
	public function setRecordingFile ($v){$this->putInCustomData(self::RECORDING_FILE, $v);}
	
}
