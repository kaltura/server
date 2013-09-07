<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlMeetingAccessControlType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $listToPublic;
	
	/**
	 *
	 * @var boolean
	 */
	protected $isPublic;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $meetingPassword;
	
	/**
	 *
	 * @var boolean
	 */
	protected $enforcePassword;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'listToPublic':
				return 'boolean';
	
			case 'isPublic':
				return 'boolean';
	
			case 'meetingPassword':
				return 'WebexXml';
	
			case 'enforcePassword':
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
			'listToPublic',
			'isPublic',
			'meetingPassword',
			'enforcePassword',
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
		return 'accessControlType';
	}
	
	/**
	 * @param boolean $listToPublic
	 */
	public function setListToPublic($listToPublic)
	{
		$this->listToPublic = $listToPublic;
	}
	
	/**
	 * @return boolean $listToPublic
	 */
	public function getListToPublic()
	{
		return $this->listToPublic;
	}
	
	/**
	 * @param boolean $isPublic
	 */
	public function setIsPublic($isPublic)
	{
		$this->isPublic = $isPublic;
	}
	
	/**
	 * @return boolean $isPublic
	 */
	public function getIsPublic()
	{
		return $this->isPublic;
	}
	
	/**
	 * @param WebexXml $meetingPassword
	 */
	public function setMeetingPassword(WebexXml $meetingPassword)
	{
		$this->meetingPassword = $meetingPassword;
	}
	
	/**
	 * @return WebexXml $meetingPassword
	 */
	public function getMeetingPassword()
	{
		return $this->meetingPassword;
	}
	
	/**
	 * @param boolean $enforcePassword
	 */
	public function setEnforcePassword($enforcePassword)
	{
		$this->enforcePassword = $enforcePassword;
	}
	
	/**
	 * @return boolean $enforcePassword
	 */
	public function getEnforcePassword()
	{
		return $this->enforcePassword;
	}
	
}
		
