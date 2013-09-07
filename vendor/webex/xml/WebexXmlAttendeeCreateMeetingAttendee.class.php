<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlAttendeeCreateMeetingAttendee extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlAttAttendeeType>
	 */
	protected $attendees;
	
	/**
	 *
	 * @var boolean
	 */
	protected $validateFormat;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'attendees':
				return 'WebexXmlArray<WebexXmlAttAttendeeType>';
	
			case 'validateFormat':
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
			'attendees',
			'validateFormat',
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
		return 'createMeetingAttendee';
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
	
	/**
	 * @param boolean $validateFormat
	 */
	public function setValidateFormat($validateFormat)
	{
		$this->validateFormat = $validateFormat;
	}
	
	/**
	 * @return boolean $validateFormat
	 */
	public function getValidateFormat()
	{
		return $this->validateFormat;
	}
	
}
		
