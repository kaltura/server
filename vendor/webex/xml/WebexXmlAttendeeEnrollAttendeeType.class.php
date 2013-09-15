<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlAttendeeEnrollAttendeeType extends WebexXmlRequestType
{
	/**
	 *
	 * @var long
	 */
	protected $attendeeID;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $enrollFields;
	
	/**
	 *
	 * @var string
	 */
	protected $domain;
	
	/**
	 *
	 * @var string
	 */
	protected $ipAddress;
	
	/**
	 *
	 * @var string
	 */
	protected $submitTime;
	
	/**
	 *
	 * @var WebexXmlAttAttendeeEnrollStatusType
	 */
	protected $status;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'attendeeID':
				return 'long';
	
			case 'enrollFields':
				return 'WebexXml';
	
			case 'domain':
				return 'string';
	
			case 'ipAddress':
				return 'string';
	
			case 'submitTime':
				return 'string';
	
			case 'status':
				return 'WebexXmlAttAttendeeEnrollStatusType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'attendeeID',
			'enrollFields',
			'domain',
			'ipAddress',
			'submitTime',
			'status',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'attendeeID',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'enrollAttendeeType';
	}
	
	/**
	 * @param long $attendeeID
	 */
	public function setAttendeeID($attendeeID)
	{
		$this->attendeeID = $attendeeID;
	}
	
	/**
	 * @return long $attendeeID
	 */
	public function getAttendeeID()
	{
		return $this->attendeeID;
	}
	
	/**
	 * @param WebexXml $enrollFields
	 */
	public function setEnrollFields(WebexXml $enrollFields)
	{
		$this->enrollFields = $enrollFields;
	}
	
	/**
	 * @return WebexXml $enrollFields
	 */
	public function getEnrollFields()
	{
		return $this->enrollFields;
	}
	
	/**
	 * @param string $domain
	 */
	public function setDomain($domain)
	{
		$this->domain = $domain;
	}
	
	/**
	 * @return string $domain
	 */
	public function getDomain()
	{
		return $this->domain;
	}
	
	/**
	 * @param string $ipAddress
	 */
	public function setIpAddress($ipAddress)
	{
		$this->ipAddress = $ipAddress;
	}
	
	/**
	 * @return string $ipAddress
	 */
	public function getIpAddress()
	{
		return $this->ipAddress;
	}
	
	/**
	 * @param string $submitTime
	 */
	public function setSubmitTime($submitTime)
	{
		$this->submitTime = $submitTime;
	}
	
	/**
	 * @return string $submitTime
	 */
	public function getSubmitTime()
	{
		return $this->submitTime;
	}
	
	/**
	 * @param WebexXmlAttAttendeeEnrollStatusType $status
	 */
	public function setStatus(WebexXmlAttAttendeeEnrollStatusType $status)
	{
		$this->status = $status;
	}
	
	/**
	 * @return WebexXmlAttAttendeeEnrollStatusType $status
	 */
	public function getStatus()
	{
		return $this->status;
	}
	
}
		
