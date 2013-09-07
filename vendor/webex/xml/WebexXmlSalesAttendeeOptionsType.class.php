<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSalesAttendeeOptionsType extends WebexXmlRequestType
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
	protected $emailInvitations;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $participantLimit;
	
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
	
			case 'emailInvitations':
				return 'boolean';
	
			case 'participantLimit':
				return 'WebexXml';
	
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
			'emailInvitations',
			'participantLimit',
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
	
	/**
	 * @param WebexXml $participantLimit
	 */
	public function setParticipantLimit(WebexXml $participantLimit)
	{
		$this->participantLimit = $participantLimit;
	}
	
	/**
	 * @return WebexXml $participantLimit
	 */
	public function getParticipantLimit()
	{
		return $this->participantLimit;
	}
	
}
		
