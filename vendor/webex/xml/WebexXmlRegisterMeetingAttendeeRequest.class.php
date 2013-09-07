<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlRegisterMeetingAttendee.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlAttRegisterAttendeeType.class.php');

class WebexXmlRegisterMeetingAttendeeRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlAttRegisterAttendeeType>
	 */
	protected $attendees;
	
	/**
	 *
	 * @var boolean
	 */
	protected $validateFormat;
	
	
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
			'attendees',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'attendee';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'attendee:registerMeetingAttendee';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlRegisterMeetingAttendee';
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlAttRegisterAttendeeType> $attendees
	 */
	public function setAttendees(WebexXmlArray $attendees)
	{
		if($attendees->getType() != 'WebexXmlAttRegisterAttendeeType')
			throw new WebexXmlException(get_class($this) . "::attendees must be of type WebexXmlAttRegisterAttendeeType");
		
		$this->attendees = $attendees;
	}
	
	/**
	 * @param boolean $validateFormat
	 */
	public function setValidateFormat($validateFormat)
	{
		$this->validateFormat = $validateFormat;
	}
	
}
		
