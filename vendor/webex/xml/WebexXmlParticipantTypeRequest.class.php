<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlParticipantType.class.php');
require_once(__DIR__ . '/WebexXmlComPersonType.class.php');
require_once(__DIR__ . '/WebexXmlSessJoinStatusType.class.php');
require_once(__DIR__ . '/WebexXmlAttRoleType.class.php');

class WebexXmlParticipantTypeRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var WebexXmlComPersonType
	 */
	protected $person;
	
	/**
	 *
	 * @var long
	 */
	protected $contactID;
	
	/**
	 *
	 * @var WebexXmlSessJoinStatusType
	 */
	protected $joinStatus;
	
	/**
	 *
	 * @var WebexXmlAttRoleType
	 */
	protected $role;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'person',
			'contactID',
			'joinStatus',
			'role',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'person',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'session';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'session:participantType';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlParticipantType';
	}
	
	/**
	 * @param WebexXmlComPersonType $person
	 */
	public function setPerson(WebexXmlComPersonType $person)
	{
		$this->person = $person;
	}
	
	/**
	 * @param long $contactID
	 */
	public function setContactID($contactID)
	{
		$this->contactID = $contactID;
	}
	
	/**
	 * @param WebexXmlSessJoinStatusType $joinStatus
	 */
	public function setJoinStatus(WebexXmlSessJoinStatusType $joinStatus)
	{
		$this->joinStatus = $joinStatus;
	}
	
	/**
	 * @param WebexXmlAttRoleType $role
	 */
	public function setRole(WebexXmlAttRoleType $role)
	{
		$this->role = $role;
	}
	
}
		
