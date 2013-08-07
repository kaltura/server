<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlGetjoinurlMeeting.class.php');
require_once(__DIR__ . '/WebexXmlComEmailType.class.php');

class WebexXmlGetjoinurlMeetingRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var long
	 */
	protected $meetingKey;
	
	/**
	 *
	 * @var long
	 */
	protected $sessionKey;
	
	/**
	 *
	 * @var string
	 */
	protected $attendeeName;
	
	/**
	 *
	 * @var WebexXmlComEmailType
	 */
	protected $attendeeEmail;
	
	/**
	 *
	 * @var string
	 */
	protected $meetingPW;
	
	/**
	 *
	 * @var string
	 */
	protected $RegID;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'meetingKey',
			'sessionKey',
			'attendeeName',
			'attendeeEmail',
			'meetingPW',
			'RegID',
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
		return 'meeting';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'meeting:getjoinurlMeeting';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlGetjoinurlMeeting';
	}
	
	/**
	 * @param long $meetingKey
	 */
	public function setMeetingKey($meetingKey)
	{
		$this->meetingKey = $meetingKey;
	}
	
	/**
	 * @param long $sessionKey
	 */
	public function setSessionKey($sessionKey)
	{
		$this->sessionKey = $sessionKey;
	}
	
	/**
	 * @param string $attendeeName
	 */
	public function setAttendeeName($attendeeName)
	{
		$this->attendeeName = $attendeeName;
	}
	
	/**
	 * @param WebexXmlComEmailType $attendeeEmail
	 */
	public function setAttendeeEmail(WebexXmlComEmailType $attendeeEmail)
	{
		$this->attendeeEmail = $attendeeEmail;
	}
	
	/**
	 * @param string $meetingPW
	 */
	public function setMeetingPW($meetingPW)
	{
		$this->meetingPW = $meetingPW;
	}
	
	/**
	 * @param string $RegID
	 */
	public function setRegID($RegID)
	{
		$this->RegID = $RegID;
	}
	
}
		
