<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlMeetingMeetingInstanceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var long
	 */
	protected $meetingkey;
	
	/**
	 *
	 * @var string
	 */
	protected $status;
	
	/**
	 *
	 * @var boolean
	 */
	protected $hostJoined;
	
	/**
	 *
	 * @var boolean
	 */
	protected $participantsJoined;
	
	/**
	 *
	 * @var boolean
	 */
	protected $telePresence;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'meetingkey':
				return 'long';
	
			case 'status':
				return 'string';
	
			case 'hostJoined':
				return 'boolean';
	
			case 'participantsJoined':
				return 'boolean';
	
			case 'telePresence':
				return 'boolean';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'meetingkey',
			'status',
			'hostJoined',
			'participantsJoined',
			'telePresence',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'meetingkey',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'meetingInstanceType';
	}
	
	/**
	 * @param long $meetingkey
	 */
	public function setMeetingkey($meetingkey)
	{
		$this->meetingkey = $meetingkey;
	}
	
	/**
	 * @return long $meetingkey
	 */
	public function getMeetingkey()
	{
		return $this->meetingkey;
	}
	
	/**
	 * @param string $status
	 */
	public function setStatus($status)
	{
		$this->status = $status;
	}
	
	/**
	 * @return string $status
	 */
	public function getStatus()
	{
		return $this->status;
	}
	
	/**
	 * @param boolean $hostJoined
	 */
	public function setHostJoined($hostJoined)
	{
		$this->hostJoined = $hostJoined;
	}
	
	/**
	 * @return boolean $hostJoined
	 */
	public function getHostJoined()
	{
		return $this->hostJoined;
	}
	
	/**
	 * @param boolean $participantsJoined
	 */
	public function setParticipantsJoined($participantsJoined)
	{
		$this->participantsJoined = $participantsJoined;
	}
	
	/**
	 * @return boolean $participantsJoined
	 */
	public function getParticipantsJoined()
	{
		return $this->participantsJoined;
	}
	
	/**
	 * @param boolean $telePresence
	 */
	public function setTelePresence($telePresence)
	{
		$this->telePresence = $telePresence;
	}
	
	/**
	 * @return boolean $telePresence
	 */
	public function getTelePresence()
	{
		return $this->telePresence;
	}
	
}
		
