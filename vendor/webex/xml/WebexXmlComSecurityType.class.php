<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlComSecurityType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $webExID;
	
	/**
	 *
	 * @var string
	 */
	protected $password;
	
	/**
	 *
	 * @var long
	 */
	protected $siteID;
	
	/**
	 *
	 * @var string
	 */
	protected $siteName;
	
	/**
	 *
	 * @var string
	 */
	protected $partnerID;
	
	/**
	 *
	 * @var string
	 */
	protected $email;
	
	/**
	 *
	 * @var string
	 */
	protected $sessionTicket;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'webExID':
				return 'string';
	
			case 'password':
				return 'string';
	
			case 'siteID':
				return 'long';
	
			case 'siteName':
				return 'string';
	
			case 'partnerID':
				return 'string';
	
			case 'email':
				return 'string';
	
			case 'sessionTicket':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'webExID',
			'password',
			'siteID',
			'siteName',
			'partnerID',
			'email',
			'sessionTicket',
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
		return 'securityType';
	}
	
	/**
	 * @param string $webExID
	 */
	public function setWebExID($webExID)
	{
		$this->webExID = $webExID;
	}
	
	/**
	 * @return string $webExID
	 */
	public function getWebExID()
	{
		return $this->webExID;
	}
	
	/**
	 * @param string $password
	 */
	public function setPassword($password)
	{
		$this->password = $password;
	}
	
	/**
	 * @return string $password
	 */
	public function getPassword()
	{
		return $this->password;
	}
	
	/**
	 * @param long $siteID
	 */
	public function setSiteID($siteID)
	{
		$this->siteID = $siteID;
	}
	
	/**
	 * @return long $siteID
	 */
	public function getSiteID()
	{
		return $this->siteID;
	}
	
	/**
	 * @param string $siteName
	 */
	public function setSiteName($siteName)
	{
		$this->siteName = $siteName;
	}
	
	/**
	 * @return string $siteName
	 */
	public function getSiteName()
	{
		return $this->siteName;
	}
	
	/**
	 * @param string $partnerID
	 */
	public function setPartnerID($partnerID)
	{
		$this->partnerID = $partnerID;
	}
	
	/**
	 * @return string $partnerID
	 */
	public function getPartnerID()
	{
		return $this->partnerID;
	}
	
	/**
	 * @param string $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}
	
	/**
	 * @return string $email
	 */
	public function getEmail()
	{
		return $this->email;
	}
	
	/**
	 * @param string $sessionTicket
	 */
	public function setSessionTicket($sessionTicket)
	{
		$this->sessionTicket = $sessionTicket;
	}
	
	/**
	 * @return string $sessionTicket
	 */
	public function getSessionTicket()
	{
		return $this->sessionTicket;
	}
	
}
		
