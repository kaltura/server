<?php
/**
 * @package plugins.dropFolder
 * @subpackage model
 */
class WebexDropFolderFile extends DropFolderFile
{
	const RECORDING_ID = 'recording_id';
	
	const WEBEX_HOST_ID = 'webex_host_id';
	
	const DESCRIPTION = 'description';
	
	const WEBEX_CONF_IF = 'webex_conf_id';
	
	/**
	 * @var int
	 */
	protected $recordingId;
	
	/**
	 * @var string
	 */
	protected $webexHostId;
	
	/**
	 * @var string
	 */
	protected $description;
	
	/**
	 * @var string
	 */
	protected $confId;
	
	/**
	 * return int
	 */
	public function getRecordingId ()
	{
		return $this->getFromCustomData(self::RECORDING_ID);
	}
	
	/**
	 * @param int $v
	 */
	public function setRecordingId ($v)
	{
		$this->putInCustomData(self::RECORDING_ID, $v);
	}
	
	/**
	 * return string
	 */
	public function getConfId ()
	{
		return $this->getFromCustomData(self::WEBEX_CONF_IF);
	}
	
	/**
	 * @param string $v
	 */
	public function setConfId ($v)
	{
		$this->putInCustomData(self::WEBEX_CONF_IF, $v);
	}
	
	/**
	 * return string
	 */
	public function getDescription ()
	{
		return $this->getFromCustomData(self::DESCRIPTION);
	}
	
	/**
	 * @param string $v
	 */
	public function setDescription ($v)
	{
		$this->putInCustomData(self::DESCRIPTION, $v);
	}
	
	/**
	 * return string
	 */
	public function getWebexHostId ()
	{
		return $this->getFromCustomData(self::WEBEX_HOST_ID);
	}
	
	/**
	 * @param string $v
	 */
	public function setWebexHostId ($v)
	{
		$this->putInCustomData(self::WEBEX_HOST_ID, $v);
	}
}
