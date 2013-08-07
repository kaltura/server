<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTeleconferenceonlyAttendeeOptionsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $requireUcfDiagnosis;
	
	/**
	 *
	 * @var boolean
	 */
	protected $excludePassword;
	
	/**
	 *
	 * @var boolean
	 */
	protected $requireAccount;
	
	/**
	 *
	 * @var boolean
	 */
	protected $emailInvitations;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'requireUcfDiagnosis':
				return 'boolean';
	
			case 'excludePassword':
				return 'boolean';
	
			case 'requireAccount':
				return 'boolean';
	
			case 'emailInvitations':
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
			'requireUcfDiagnosis',
			'excludePassword',
			'requireAccount',
			'emailInvitations',
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
		return 'attendeeOptionsType';
	}
	
	/**
	 * @param boolean $requireUcfDiagnosis
	 */
	public function setRequireUcfDiagnosis($requireUcfDiagnosis)
	{
		$this->requireUcfDiagnosis = $requireUcfDiagnosis;
	}
	
	/**
	 * @return boolean $requireUcfDiagnosis
	 */
	public function getRequireUcfDiagnosis()
	{
		return $this->requireUcfDiagnosis;
	}
	
	/**
	 * @param boolean $excludePassword
	 */
	public function setExcludePassword($excludePassword)
	{
		$this->excludePassword = $excludePassword;
	}
	
	/**
	 * @return boolean $excludePassword
	 */
	public function getExcludePassword()
	{
		return $this->excludePassword;
	}
	
	/**
	 * @param boolean $requireAccount
	 */
	public function setRequireAccount($requireAccount)
	{
		$this->requireAccount = $requireAccount;
	}
	
	/**
	 * @return boolean $requireAccount
	 */
	public function getRequireAccount()
	{
		return $this->requireAccount;
	}
	
	/**
	 * @param boolean $emailInvitations
	 */
	public function setEmailInvitations($emailInvitations)
	{
		$this->emailInvitations = $emailInvitations;
	}
	
	/**
	 * @return boolean $emailInvitations
	 */
	public function getEmailInvitations()
	{
		return $this->emailInvitations;
	}
	
}
		
