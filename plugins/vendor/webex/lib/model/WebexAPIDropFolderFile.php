<?php
/**
 * @package plugins.dropFolder
 * @subpackage model
 */
class WebexAPIDropFolderFile extends DropFolderFile
{
	const RECORDING_ID = 'recording_id';
	const DESCRIPTION = 'description';
	const CONTENT_URL = 'content_url';
	const URL_EXPIRY = 'url_expiry';
	const FILE_EXTENSION = 'file_extension';
	const MEETING_ID = 'meeting_id';
	const RECORDING_START_TIME = 'recording_start_time';
	
	/**
	 * @var int
	 */
	protected $recordingId;
	
	/**
	 * @var string
	 */
	protected $description;
	
	/**
	 * @var string
	 */
	protected $contentUrl;
	
	/**
	 * @var int
	 */
	protected $urlExpiry;
	
	/**
	 * @var string
	 */
	protected $fileExtension;
	
	/**
	 * @var string
	 */
	protected $meetingId;
	
	/**
	 * @var int
	 */
	protected $recordingStartTime;


	/**
	 * @return int
	 */
	public function getRecordingId()
	{
		return $this->getFromCustomData(self::RECORDING_ID);
	}
	
	/**
	 * @param int $v
	 */
	public function setRecordingId($v)
	{
		$this->putInCustomData(self::RECORDING_ID, $v);
	}
	
	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->getFromCustomData(self::DESCRIPTION);
	}
	
	/**
	 * @param string $v
	 */
	public function setDescription($v)
	{
		$this->putInCustomData(self::DESCRIPTION, $v);
	}
	
	/**
	 * @return string
	 */
	public function getContentUrl()
	{
		return $this->getFromCustomData(self::CONTENT_URL);
	}
	
	/**
	 * @param string $v
	 */
	public function setContentUrl($v)
	{
		$this->putInCustomData(self::CONTENT_URL, $v);
	}
	
	/**
	 * @return int
	 */
	public function getUrlExpiry()
	{
		return $this->getFromCustomData(self::URL_EXPIRY);
	}
	
	/**
	 * @param int $v
	 */
	public function setUrlExpiry($v)
	{
		$this->putInCustomData(self::URL_EXPIRY, $v);
	}
	
	/**
	 * @return string
	 */
	public function getFileExtension()
	{
		return $this->getFromCustomData(self::FILE_EXTENSION);
	}
	
	/**
	 * @param string $v
	 */
	public function setFileExtension($v)
	{
		$this->putInCustomData(self::FILE_EXTENSION, $v);
	}
	
	/**
	 * @return string
	 */
	public function getMeetingId()
	{
		return $this->getFromCustomData(self::MEETING_ID);
	}
	
	/**
	 * @param string $v
	 */
	public function setMeetingId($v)
	{
		$this->putInCustomData(self::MEETING_ID, $v);
	}
	
	/**
	 * @return int
	 */
	public function getRecordingStartTime()
	{
		return $this->getFromCustomData(self::RECORDING_START_TIME);
	}
	
	/**
	 * @param int $v
	 */
	public function setRecordingStartTime($v)
	{
		$this->putInCustomData(self::RECORDING_START_TIME, $v);
	}
	
	public function getFileUrl ()
	{
		return $this->getContentUrl();
	} 
	
	public function getNameForParsing ()
	{
		return str_replace('_'.$this->getRecordingId(), '', $this->getFileName());
	}
	
	public function setParsedSlug ($v)
	{
		$v .= '_'.$this->getRecordingId();
		parent::setParsedSlug($v);
	}

}
