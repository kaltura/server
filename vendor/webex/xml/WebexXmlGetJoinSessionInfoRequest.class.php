<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlGetJoinSessionInfo.class.php');
require_once(__DIR__ . '/WebexXmlComEmailType.class.php');

class WebexXmlGetJoinSessionInfoRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var long
	 */
	protected $sessionKey;
	
	/**
	 *
	 * @var string
	 */
	protected $sessionPassword;
	
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
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'sessionKey',
			'sessionPassword',
			'attendeeName',
			'attendeeEmail',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'sessionKey',
			'sessionPassword',
			'attendeeName',
			'attendeeEmail',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'ep';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'ep:getJoinSessionInfo';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlGetJoinSessionInfo';
	}
	
	/**
	 * @param long $sessionKey
	 */
	public function setSessionKey($sessionKey)
	{
		$this->sessionKey = $sessionKey;
	}
	
	/**
	 * @param string $sessionPassword
	 */
	public function setSessionPassword($sessionPassword)
	{
		$this->sessionPassword = $sessionPassword;
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
	
}
		
