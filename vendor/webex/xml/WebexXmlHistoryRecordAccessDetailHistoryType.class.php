<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlHistoryRecordAccessDetailHistoryType extends WebexXmlRequestType
{
	/**
	 *
	 * @var long
	 */
	protected $viewID;
	
	/**
	 *
	 * @var string
	 */
	protected $participantName;
	
	/**
	 *
	 * @var string
	 */
	protected $participantEmail;
	
	/**
	 *
	 * @var string
	 */
	protected $accessTime;
	
	/**
	 *
	 * @var boolean
	 */
	protected $registered;
	
	/**
	 *
	 * @var string
	 */
	protected $registerDate;
	
	/**
	 *
	 * @var boolean
	 */
	protected $downloaded;
	
	/**
	 *
	 * @var boolean
	 */
	protected $viewed;
	
	/**
	 *
	 * @var int
	 */
	protected $timeZoneID;
	
	/**
	 *
	 * @var WebexXmlHistoryRegFieldsType
	 */
	protected $regFields;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'viewID':
				return 'long';
	
			case 'participantName':
				return 'string';
	
			case 'participantEmail':
				return 'string';
	
			case 'accessTime':
				return 'string';
	
			case 'registered':
				return 'boolean';
	
			case 'registerDate':
				return 'string';
	
			case 'downloaded':
				return 'boolean';
	
			case 'viewed':
				return 'boolean';
	
			case 'timeZoneID':
				return 'int';
	
			case 'regFields':
				return 'WebexXmlHistoryRegFieldsType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'viewID',
			'participantName',
			'participantEmail',
			'accessTime',
			'registered',
			'registerDate',
			'downloaded',
			'viewed',
			'timeZoneID',
			'regFields',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'recordAccessDetailHistoryType';
	}
	
	/**
	 * @param long $viewID
	 */
	public function setViewID($viewID)
	{
		$this->viewID = $viewID;
	}
	
	/**
	 * @return long $viewID
	 */
	public function getViewID()
	{
		return $this->viewID;
	}
	
	/**
	 * @param string $participantName
	 */
	public function setParticipantName($participantName)
	{
		$this->participantName = $participantName;
	}
	
	/**
	 * @return string $participantName
	 */
	public function getParticipantName()
	{
		return $this->participantName;
	}
	
	/**
	 * @param string $participantEmail
	 */
	public function setParticipantEmail($participantEmail)
	{
		$this->participantEmail = $participantEmail;
	}
	
	/**
	 * @return string $participantEmail
	 */
	public function getParticipantEmail()
	{
		return $this->participantEmail;
	}
	
	/**
	 * @param string $accessTime
	 */
	public function setAccessTime($accessTime)
	{
		$this->accessTime = $accessTime;
	}
	
	/**
	 * @return string $accessTime
	 */
	public function getAccessTime()
	{
		return $this->accessTime;
	}
	
	/**
	 * @param boolean $registered
	 */
	public function setRegistered($registered)
	{
		$this->registered = $registered;
	}
	
	/**
	 * @return boolean $registered
	 */
	public function getRegistered()
	{
		return $this->registered;
	}
	
	/**
	 * @param string $registerDate
	 */
	public function setRegisterDate($registerDate)
	{
		$this->registerDate = $registerDate;
	}
	
	/**
	 * @return string $registerDate
	 */
	public function getRegisterDate()
	{
		return $this->registerDate;
	}
	
	/**
	 * @param boolean $downloaded
	 */
	public function setDownloaded($downloaded)
	{
		$this->downloaded = $downloaded;
	}
	
	/**
	 * @return boolean $downloaded
	 */
	public function getDownloaded()
	{
		return $this->downloaded;
	}
	
	/**
	 * @param boolean $viewed
	 */
	public function setViewed($viewed)
	{
		$this->viewed = $viewed;
	}
	
	/**
	 * @return boolean $viewed
	 */
	public function getViewed()
	{
		return $this->viewed;
	}
	
	/**
	 * @param int $timeZoneID
	 */
	public function setTimeZoneID($timeZoneID)
	{
		$this->timeZoneID = $timeZoneID;
	}
	
	/**
	 * @return int $timeZoneID
	 */
	public function getTimeZoneID()
	{
		return $this->timeZoneID;
	}
	
	/**
	 * @param WebexXmlHistoryRegFieldsType $regFields
	 */
	public function setRegFields(WebexXmlHistoryRegFieldsType $regFields)
	{
		$this->regFields = $regFields;
	}
	
	/**
	 * @return WebexXmlHistoryRegFieldsType $regFields
	 */
	public function getRegFields()
	{
		return $this->regFields;
	}
	
}
		
