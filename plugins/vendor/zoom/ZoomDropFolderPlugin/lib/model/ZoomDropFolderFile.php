<?php
/**
 * @package plugins.dropFolder
 * @subpackage model
 */
class ZoomDropFolderFile extends DropFolderFile
{
	const MEETING_METADATA = 'meetingMetadata';
	const RECORDING_FILE = 'recordingFile';
	const PARENT_ENTRY_ID = 'parentEntryId';
	const IS_PARENT_ENTRY = 'isParentEntry';
		
		/**
	 * @var ZoomMeetingMetadata
	 */
	protected $meetingMetadata;

	/**
	 * @var ZoomRecordingFile
	 */
	protected $recordingFile;
	
	/**
	 * @var string
	 */
	protected $parentEntryId;
	
	/**
	 * @var bool
	 */
	protected $isParentEntry;
	
	/**
	 * return ZoomMeetingMetadata
	 */
	public function getMeetingMetadata() {return $this->getFromCustomData(self::MEETING_METADATA);}
	
	/**
	 * @param ZoomMeetingMetadata $v
	 */
	public function setMeetingMetadata ($v){$this->putInCustomData(self::MEETING_METADATA, $v);}
	
	/**
	 * return ZoomRecordingFile
	 */
	public function getRecordingFile() {return $this->getFromCustomData(self::RECORDING_FILE);}

	/**
	 * @param ZoomRecordingFile $v
	 */
	public function setRecordingFile ($v){$this->putInCustomData(self::RECORDING_FILE, $v);}
	
	/**
	 * return string
	 */
	public function getParentEntryId() {return $this->getFromCustomData(self::PARENT_ENTRY_ID);}
	
	/**
	 * @param string $v
	 */
	public function setParentEntryId ($v){$this->putInCustomData(self::PARENT_ENTRY_ID, $v);}
	
	/**
	 * return bool
	 */
	public function getIsParentEntry() {return $this->getFromCustomData(self::IS_PARENT_ENTRY);}
	
	/**
	 * @param bool $v
	 */
	public function setIsParentEntry ($v){$this->putInCustomData(self::IS_PARENT_ENTRY, $v);}
	
}
