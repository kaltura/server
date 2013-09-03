<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlDelMeetingAttendee.class.php');
require_once(__DIR__ . '/long.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlAttAttendeeEmailType.class.php');

class WebexXmlDelMeetingAttendeeRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlArray<long>
	 */
	protected $attendeeID;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlAttAttendeeEmailType>
	 */
	protected $attendeeEmail;
	
	/**
	 *
	 * @var boolean
	 */
	protected $sendEmail;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'attendeeID',
			'attendeeEmail',
			'sendEmail',
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
		return 'attendee:delMeetingAttendee';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlDelMeetingAttendee';
	}
	
	/**
	 * @param WebexXmlArray<long> $attendeeID
	 */
	public function setAttendeeID($attendeeID)
	{
		if($attendeeID->getType() != 'long')
			throw new WebexXmlException(get_class($this) . "::attendeeID must be of type long");
		
		$this->attendeeID = $attendeeID;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlAttAttendeeEmailType> $attendeeEmail
	 */
	public function setAttendeeEmail(WebexXmlArray $attendeeEmail)
	{
		if($attendeeEmail->getType() != 'WebexXmlAttAttendeeEmailType')
			throw new WebexXmlException(get_class($this) . "::attendeeEmail must be of type WebexXmlAttAttendeeEmailType");
		
		$this->attendeeEmail = $attendeeEmail;
	}
	
	/**
	 * @param boolean $sendEmail
	 */
	public function setSendEmail($sendEmail)
	{
		$this->sendEmail = $sendEmail;
	}
	
}
		
