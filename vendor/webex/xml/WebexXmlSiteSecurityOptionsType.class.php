<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteSecurityOptionsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $passwordExpires;
	
	/**
	 *
	 * @var integer
	 */
	protected $passwordLifetime;
	
	/**
	 *
	 * @var boolean
	 */
	protected $allMeetingsUnlisted;
	
	/**
	 *
	 * @var boolean
	 */
	protected $allMeetingsPassword;
	
	/**
	 *
	 * @var boolean
	 */
	protected $joinBeforeHost;
	
	/**
	 *
	 * @var boolean
	 */
	protected $audioBeforeHost;
	
	/**
	 *
	 * @var boolean
	 */
	protected $changePersonalURL;
	
	/**
	 *
	 * @var boolean
	 */
	protected $changeUserName;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $meetings;
	
	/**
	 *
	 * @var boolean
	 */
	protected $strictUserPassword;
	
	/**
	 *
	 * @var boolean
	 */
	protected $accountNotify;
	
	/**
	 *
	 * @var boolean
	 */
	protected $requireLoginBeforeSiteAccess;
	
	/**
	 *
	 * @var boolean
	 */
	protected $changePWDWhenAutoLogin;
	
	/**
	 *
	 * @var boolean
	 */
	protected $enforceBaseline;
	
	/**
	 *
	 * @var boolean
	 */
	protected $passwordChangeIntervalOpt;
	
	/**
	 *
	 * @var integer
	 */
	protected $passwordChangeInterval;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'passwordExpires':
				return 'boolean';
	
			case 'passwordLifetime':
				return 'integer';
	
			case 'allMeetingsUnlisted':
				return 'boolean';
	
			case 'allMeetingsPassword':
				return 'boolean';
	
			case 'joinBeforeHost':
				return 'boolean';
	
			case 'audioBeforeHost':
				return 'boolean';
	
			case 'changePersonalURL':
				return 'boolean';
	
			case 'changeUserName':
				return 'boolean';
	
			case 'meetings':
				return 'WebexXml';
	
			case 'strictUserPassword':
				return 'boolean';
	
			case 'accountNotify':
				return 'boolean';
	
			case 'requireLoginBeforeSiteAccess':
				return 'boolean';
	
			case 'changePWDWhenAutoLogin':
				return 'boolean';
	
			case 'enforceBaseline':
				return 'boolean';
	
			case 'passwordChangeIntervalOpt':
				return 'boolean';
	
			case 'passwordChangeInterval':
				return 'integer';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'passwordExpires',
			'passwordLifetime',
			'allMeetingsUnlisted',
			'allMeetingsPassword',
			'joinBeforeHost',
			'audioBeforeHost',
			'changePersonalURL',
			'changeUserName',
			'meetings',
			'strictUserPassword',
			'accountNotify',
			'requireLoginBeforeSiteAccess',
			'changePWDWhenAutoLogin',
			'enforceBaseline',
			'passwordChangeIntervalOpt',
			'passwordChangeInterval',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'passwordExpires',
			'passwordLifetime',
			'allMeetingsUnlisted',
			'allMeetingsPassword',
			'joinBeforeHost',
			'audioBeforeHost',
			'changePersonalURL',
			'changeUserName',
			'meetings',
			'strictUserPassword',
			'changePWDWhenAutoLogin',
			'enforceBaseline',
			'passwordChangeIntervalOpt',
			'passwordChangeInterval',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'securityOptionsType';
	}
	
	/**
	 * @param boolean $passwordExpires
	 */
	public function setPasswordExpires($passwordExpires)
	{
		$this->passwordExpires = $passwordExpires;
	}
	
	/**
	 * @return boolean $passwordExpires
	 */
	public function getPasswordExpires()
	{
		return $this->passwordExpires;
	}
	
	/**
	 * @param integer $passwordLifetime
	 */
	public function setPasswordLifetime($passwordLifetime)
	{
		$this->passwordLifetime = $passwordLifetime;
	}
	
	/**
	 * @return integer $passwordLifetime
	 */
	public function getPasswordLifetime()
	{
		return $this->passwordLifetime;
	}
	
	/**
	 * @param boolean $allMeetingsUnlisted
	 */
	public function setAllMeetingsUnlisted($allMeetingsUnlisted)
	{
		$this->allMeetingsUnlisted = $allMeetingsUnlisted;
	}
	
	/**
	 * @return boolean $allMeetingsUnlisted
	 */
	public function getAllMeetingsUnlisted()
	{
		return $this->allMeetingsUnlisted;
	}
	
	/**
	 * @param boolean $allMeetingsPassword
	 */
	public function setAllMeetingsPassword($allMeetingsPassword)
	{
		$this->allMeetingsPassword = $allMeetingsPassword;
	}
	
	/**
	 * @return boolean $allMeetingsPassword
	 */
	public function getAllMeetingsPassword()
	{
		return $this->allMeetingsPassword;
	}
	
	/**
	 * @param boolean $joinBeforeHost
	 */
	public function setJoinBeforeHost($joinBeforeHost)
	{
		$this->joinBeforeHost = $joinBeforeHost;
	}
	
	/**
	 * @return boolean $joinBeforeHost
	 */
	public function getJoinBeforeHost()
	{
		return $this->joinBeforeHost;
	}
	
	/**
	 * @param boolean $audioBeforeHost
	 */
	public function setAudioBeforeHost($audioBeforeHost)
	{
		$this->audioBeforeHost = $audioBeforeHost;
	}
	
	/**
	 * @return boolean $audioBeforeHost
	 */
	public function getAudioBeforeHost()
	{
		return $this->audioBeforeHost;
	}
	
	/**
	 * @param boolean $changePersonalURL
	 */
	public function setChangePersonalURL($changePersonalURL)
	{
		$this->changePersonalURL = $changePersonalURL;
	}
	
	/**
	 * @return boolean $changePersonalURL
	 */
	public function getChangePersonalURL()
	{
		return $this->changePersonalURL;
	}
	
	/**
	 * @param boolean $changeUserName
	 */
	public function setChangeUserName($changeUserName)
	{
		$this->changeUserName = $changeUserName;
	}
	
	/**
	 * @return boolean $changeUserName
	 */
	public function getChangeUserName()
	{
		return $this->changeUserName;
	}
	
	/**
	 * @param WebexXml $meetings
	 */
	public function setMeetings(WebexXml $meetings)
	{
		$this->meetings = $meetings;
	}
	
	/**
	 * @return WebexXml $meetings
	 */
	public function getMeetings()
	{
		return $this->meetings;
	}
	
	/**
	 * @param boolean $strictUserPassword
	 */
	public function setStrictUserPassword($strictUserPassword)
	{
		$this->strictUserPassword = $strictUserPassword;
	}
	
	/**
	 * @return boolean $strictUserPassword
	 */
	public function getStrictUserPassword()
	{
		return $this->strictUserPassword;
	}
	
	/**
	 * @param boolean $accountNotify
	 */
	public function setAccountNotify($accountNotify)
	{
		$this->accountNotify = $accountNotify;
	}
	
	/**
	 * @return boolean $accountNotify
	 */
	public function getAccountNotify()
	{
		return $this->accountNotify;
	}
	
	/**
	 * @param boolean $requireLoginBeforeSiteAccess
	 */
	public function setRequireLoginBeforeSiteAccess($requireLoginBeforeSiteAccess)
	{
		$this->requireLoginBeforeSiteAccess = $requireLoginBeforeSiteAccess;
	}
	
	/**
	 * @return boolean $requireLoginBeforeSiteAccess
	 */
	public function getRequireLoginBeforeSiteAccess()
	{
		return $this->requireLoginBeforeSiteAccess;
	}
	
	/**
	 * @param boolean $changePWDWhenAutoLogin
	 */
	public function setChangePWDWhenAutoLogin($changePWDWhenAutoLogin)
	{
		$this->changePWDWhenAutoLogin = $changePWDWhenAutoLogin;
	}
	
	/**
	 * @return boolean $changePWDWhenAutoLogin
	 */
	public function getChangePWDWhenAutoLogin()
	{
		return $this->changePWDWhenAutoLogin;
	}
	
	/**
	 * @param boolean $enforceBaseline
	 */
	public function setEnforceBaseline($enforceBaseline)
	{
		$this->enforceBaseline = $enforceBaseline;
	}
	
	/**
	 * @return boolean $enforceBaseline
	 */
	public function getEnforceBaseline()
	{
		return $this->enforceBaseline;
	}
	
	/**
	 * @param boolean $passwordChangeIntervalOpt
	 */
	public function setPasswordChangeIntervalOpt($passwordChangeIntervalOpt)
	{
		$this->passwordChangeIntervalOpt = $passwordChangeIntervalOpt;
	}
	
	/**
	 * @return boolean $passwordChangeIntervalOpt
	 */
	public function getPasswordChangeIntervalOpt()
	{
		return $this->passwordChangeIntervalOpt;
	}
	
	/**
	 * @param integer $passwordChangeInterval
	 */
	public function setPasswordChangeInterval($passwordChangeInterval)
	{
		$this->passwordChangeInterval = $passwordChangeInterval;
	}
	
	/**
	 * @return integer $passwordChangeInterval
	 */
	public function getPasswordChangeInterval()
	{
		return $this->passwordChangeInterval;
	}
	
}
		
