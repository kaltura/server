<?php
/**
 * Meeting Metadata
 *
 * @package plugins.ZoomDropFolder
 * @subpackage model
 *
 */
class kMeetingMetadata
{
	const UUID = 'uuid';
	const MEETING_ID = 'meetingId';
	const ACCOUNT_ID = 'accountId';
	const HOST_ID = 'hostId';
	const TOPIC = 'topic';
	const MEETING_START_TIME = 'meetingStartTime';
	const TYPE = 'type';
	
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
	public function getUuid() {return $this->getFromCustomData(self::UUID);}
	
	/**
	 * @param string $v
	 */
	public function setUuid ($v){$this->putInCustomData(self::UUID, $v);}
	
	/**
	 * return string
	 */
	public function getMeetingId() {return $this->getFromCustomData(self::MEETING_ID);}
	
	/**
	 * @param string $v
	 */
	public function setMeetingId ($v){$this->putInCustomData(self::MEETING_ID, $v);}
	
	
	/**
	 * return string
	 */
	public function getAccountId (){return $this->getFromCustomData(self::ACCOUNT_ID);}
	
	/**
	 * @param string $v
	 */
	public function setAccountId ($v){$this->putInCustomData(self::ACCOUNT_ID, $v);}
	
	/**
	 * return string
	 */
	public function getHostId (){return $this->getFromCustomData(self::HOST_ID);}
	
	/**
	 * @param string $v
	 */
	public function setHostId ($v){$this->putInCustomData(self::HOST_ID, $v);}
	
	/**
	 * return string
	 */
	public function getTopic() {return $this->getFromCustomData(self::TOPIC);}
	
	/**
	 * @param string string
	 */
	public function setTopic ($v){$this->putInCustomData(self::TOPIC, $v);}
	
	/**
	 * return string
	 */
	public function getMeetingStartTime() {return $this->getFromCustomData(self::MEETING_START_TIME);}
	
	/**
	 * @param string $v
	 */
	public function setMeetingStartTime ($v){$this->putInCustomData(self::MEETING_START_TIME, $v);}
	
	/**
	 * return kRecordingType
	 */
	public function getType() {return $this->getFromCustomData(self::TYPE);}
	
	/**
	 * @param kRecordingType $v
	 */
	public function setType ($v){$this->putInCustomData(self::TYPE, $v);}
}