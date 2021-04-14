<?php
/**
 * Meeting Metadata
 *
 * @package plugins.ZoomDropFolder
 * @subpackage model
 *
 */
class ZoomMeetingMetadata
{
	/**
	 * @var string
	 */
	protected $uuid;
	
	/**
	 * @var string
	 */
	protected $meetingId;
	
	/**
	 * @var string
	 */
	protected $accountId;
	
	/**
	 * @var string
	 */
	protected $hostId;
	
	/**
	 * @var string
	 */
	protected $topic;
	
	/**
	 * @var string
	 */
	protected $meetingStartTime;
	
	/**
	 * @var kRecordingType
	 */
	protected $type;
	
	/**
	 * return string
	 */
	public function getUuid()
	{
		return $this->uuid;
	}
	
	/**
	 * @param string $v
	 */
	public function setUuid ($v)
	{
		$this->uuid = $v;
	}
	
	/**
	 * return string
	 */
	public function getMeetingId()
	{
		return $this->meetingId;
	}
	
	/**
	 * @param string $v
	 */
	public function setMeetingId($v)
	{
		$this->meetingId = $v;
	}
	
	/**
	 * return string
	 */
	public function getAccountId()
	{
		return $this->accountId;
	}
	
	/**
	 * @param string $v
	 */
	public function setAccountId($v)
	{
		$this->accountId = $v;
	}
	
	/**
	 * return string
	 */
	public function getHostId()
	{
		return $this->hostId;
	}
	
	/**
	 * @param string $v
	 */
	public function setHostId($v)
	{
		$this->hostId = $v;
	}
	
	/**
	 * return string
	 */
	public function getTopic()
	{
		return $this->topic;
	}
	
	/**
	 * @param string string
	 */
	public function setTopic($v)
	{
		$this->topic = $v;
	}
	
	/**
	 * return string
	 */
	public function getMeetingStartTime()
	{
		return $this->meetingStartTime;
	}
	
	/**
	 * @param string $v
	 */
	public function setMeetingStartTime($v)
	{
		$this->meetingStartTime = $v;
	}
	
	/**
	 * return kRecordingType
	 */
	public function getType()
	{
		return $this->type;
	}
	
	/**
	 * @param kRecordingType $v
	 */
	public function setType($v)
	{
		$this->type = $v;
	}
}