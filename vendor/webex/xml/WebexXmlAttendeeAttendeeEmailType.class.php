<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlAttendeeAttendeeEmailType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlComEmailType
	 */
	protected $email;
	
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
			case 'email':
				return 'WebexXmlComEmailType';
	
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
			'email',
			'sessionKey',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'email',
			'sessionKey',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'attendeeEmailType';
	}
	
	/**
	 * @param WebexXmlComEmailType $email
	 */
	public function setEmail(WebexXmlComEmailType $email)
	{
		$this->email = $email;
	}
	
	/**
	 * @return WebexXmlComEmailType $email
	 */
	public function getEmail()
	{
		return $this->email;
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

