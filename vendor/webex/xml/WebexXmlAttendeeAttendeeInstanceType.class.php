<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlAttendeeAttendeeInstanceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var long
	 */
	protected $attendeeId;
	
	/**
	 *
	 * @var long
	 */
	protected $confID;
	
	/**
	 *
	 * @var WebexXmlAttAttendeeStatusType
	 */
	protected $status;
	
	/**
	 *
	 * @var long
	 */
	protected $registerID;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'attendeeId':
				return 'long';
	
			case 'confID':
				return 'long';
	
			case 'status':
				return 'WebexXmlAttAttendeeStatusType';
	
			case 'registerID':
				return 'long';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'attendeeId',
			'confID',
			'status',
			'registerID',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'attendeeId',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'attendeeInstanceType';
	}
	
	/**
	 * @param long $attendeeId
	 */
	public function setAttendeeId($attendeeId)
	{
		$this->attendeeId = $attendeeId;
	}
	
	/**
	 * @return long $attendeeId
	 */
	public function getAttendeeId()
	{
		return $this->attendeeId;
	}
	
	/**
	 * @param long $confID
	 */
	public function setConfID($confID)
	{
		$this->confID = $confID;
	}
	
	/**
	 * @return long $confID
	 */
	public function getConfID()
	{
		return $this->confID;
	}
	
	/**
	 * @param WebexXmlAttAttendeeStatusType $status
	 */
	public function setStatus(WebexXmlAttAttendeeStatusType $status)
	{
		$this->status = $status;
	}
	
	/**
	 * @return WebexXmlAttAttendeeStatusType $status
	 */
	public function getStatus()
	{
		return $this->status;
	}
	
	/**
	 * @param long $registerID
	 */
	public function setRegisterID($registerID)
	{
		$this->registerID = $registerID;
	}
	
	/**
	 * @return long $registerID
	 */
	public function getRegisterID()
	{
		return $this->registerID;
	}
	
}
		
