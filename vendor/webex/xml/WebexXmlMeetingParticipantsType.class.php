<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlMeetingParticipantsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var long
	 */
	protected $maxUserNumber;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlAttAttendeeType>
	 */
	protected $attendees;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'maxUserNumber':
				return 'long';
	
			case 'attendees':
				return 'WebexXmlArray<WebexXmlAttAttendeeType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'maxUserNumber',
			'attendees',
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
		return 'participantsType';
	}
	
	/**
	 * @param long $maxUserNumber
	 */
	public function setMaxUserNumber($maxUserNumber)
	{
		$this->maxUserNumber = $maxUserNumber;
	}
	
	/**
	 * @return long $maxUserNumber
	 */
	public function getMaxUserNumber()
	{
		return $this->maxUserNumber;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlAttAttendeeType> $attendees
	 */
	public function setAttendees(WebexXmlArray $attendees)
	{
		if($attendees->getType() != 'WebexXmlAttAttendeeType')
			throw new WebexXmlException(get_class($this) . "::attendees must be of type WebexXmlAttAttendeeType");
		
		$this->attendees = $attendees;
	}
	
	/**
	 * @return WebexXmlArray $attendees
	 */
	public function getAttendees()
	{
		return $this->attendees;
	}
	
}
		
