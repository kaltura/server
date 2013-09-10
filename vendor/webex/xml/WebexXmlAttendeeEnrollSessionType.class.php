<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlAttendeeEnrollSessionType extends WebexXmlRequestType
{
	/**
	 *
	 * @var long
	 */
	protected $confID;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlAttEnrollAttendeeType>
	 */
	protected $attendee;
	
	/**
	 *
	 * @var WebexXmlServMatchingRecordsType
	 */
	protected $matchingRecords;
	
	/**
	 *
	 * @var long
	 */
	protected $sessionKey;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'confID':
				return 'long';
	
			case 'attendee':
				return 'WebexXmlArray<WebexXmlAttEnrollAttendeeType>';
	
			case 'matchingRecords':
				return 'WebexXmlServMatchingRecordsType';
	
			case 'sessionKey':
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
			'confID',
			'attendee',
			'matchingRecords',
			'sessionKey',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'confID',
			'sessionKey',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'enrollSessionType';
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
	 * @param WebexXmlArray<WebexXmlAttEnrollAttendeeType> $attendee
	 */
	public function setAttendee(WebexXmlArray $attendee)
	{
		if($attendee->getType() != 'WebexXmlAttEnrollAttendeeType')
			throw new WebexXmlException(get_class($this) . "::attendee must be of type WebexXmlAttEnrollAttendeeType");
		
		$this->attendee = $attendee;
	}
	
	/**
	 * @return WebexXmlArray $attendee
	 */
	public function getAttendee()
	{
		return $this->attendee;
	}
	
	/**
	 * @param WebexXmlServMatchingRecordsType $matchingRecords
	 */
	public function setMatchingRecords(WebexXmlServMatchingRecordsType $matchingRecords)
	{
		$this->matchingRecords = $matchingRecords;
	}
	
	/**
	 * @return WebexXmlServMatchingRecordsType $matchingRecords
	 */
	public function getMatchingRecords()
	{
		return $this->matchingRecords;
	}
	
	/**
	 * @param long $sessionKey
	 */
	public function setSessionKey($sessionKey)
	{
		$this->sessionKey = $sessionKey;
	}
	
	/**
	 * @return long $sessionKey
	 */
	public function getSessionKey()
	{
		return $this->sessionKey;
	}
	
}
		
