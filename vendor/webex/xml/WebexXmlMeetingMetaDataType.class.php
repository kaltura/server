<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlMeetingMetaDataType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $confName;
	
	/**
	 *
	 * @var long
	 */
	protected $meetingType;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $agenda;
	
	/**
	 *
	 * @var string
	 */
	protected $greeting;
	
	/**
	 *
	 * @var string
	 */
	protected $location;
	
	/**
	 *
	 * @var string
	 */
	protected $invitation;
	
	/**
	 *
	 * @var boolean
	 */
	protected $setNonMTOptions;
	
	/**
	 *
	 * @var WebexXmlComSessionTemplateType
	 */
	protected $sessionTemplate;
	
	/**
	 *
	 * @var boolean
	 */
	protected $isInternal;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'confName':
				return 'string';
	
			case 'meetingType':
				return 'long';
	
			case 'agenda':
				return 'WebexXml';
	
			case 'greeting':
				return 'string';
	
			case 'location':
				return 'string';
	
			case 'invitation':
				return 'string';
	
			case 'setNonMTOptions':
				return 'boolean';
	
			case 'sessionTemplate':
				return 'WebexXmlComSessionTemplateType';
	
			case 'isInternal':
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
			'confName',
			'meetingType',
			'agenda',
			'greeting',
			'location',
			'invitation',
			'setNonMTOptions',
			'sessionTemplate',
			'isInternal',
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
		return 'metaDataType';
	}
	
	/**
	 * @param string $confName
	 */
	public function setConfName($confName)
	{
		$this->confName = $confName;
	}
	
	/**
	 * @return string $confName
	 */
	public function getConfName()
	{
		return $this->confName;
	}
	
	/**
	 * @param long $meetingType
	 */
	public function setMeetingType($meetingType)
	{
		$this->meetingType = $meetingType;
	}
	
	/**
	 * @return long $meetingType
	 */
	public function getMeetingType()
	{
		return $this->meetingType;
	}
	
	/**
	 * @param WebexXml $agenda
	 */
	public function setAgenda(WebexXml $agenda)
	{
		$this->agenda = $agenda;
	}
	
	/**
	 * @return WebexXml $agenda
	 */
	public function getAgenda()
	{
		return $this->agenda;
	}
	
	/**
	 * @param string $greeting
	 */
	public function setGreeting($greeting)
	{
		$this->greeting = $greeting;
	}
	
	/**
	 * @return string $greeting
	 */
	public function getGreeting()
	{
		return $this->greeting;
	}
	
	/**
	 * @param string $location
	 */
	public function setLocation($location)
	{
		$this->location = $location;
	}
	
	/**
	 * @return string $location
	 */
	public function getLocation()
	{
		return $this->location;
	}
	
	/**
	 * @param string $invitation
	 */
	public function setInvitation($invitation)
	{
		$this->invitation = $invitation;
	}
	
	/**
	 * @return string $invitation
	 */
	public function getInvitation()
	{
		return $this->invitation;
	}
	
	/**
	 * @param boolean $setNonMTOptions
	 */
	public function setSetNonMTOptions($setNonMTOptions)
	{
		$this->setNonMTOptions = $setNonMTOptions;
	}
	
	/**
	 * @return boolean $setNonMTOptions
	 */
	public function getSetNonMTOptions()
	{
		return $this->setNonMTOptions;
	}
	
	/**
	 * @param WebexXmlComSessionTemplateType $sessionTemplate
	 */
	public function setSessionTemplate(WebexXmlComSessionTemplateType $sessionTemplate)
	{
		$this->sessionTemplate = $sessionTemplate;
	}
	
	/**
	 * @return WebexXmlComSessionTemplateType $sessionTemplate
	 */
	public function getSessionTemplate()
	{
		return $this->sessionTemplate;
	}
	
	/**
	 * @param boolean $isInternal
	 */
	public function setIsInternal($isInternal)
	{
		$this->isInternal = $isInternal;
	}
	
	/**
	 * @return boolean $isInternal
	 */
	public function getIsInternal()
	{
		return $this->isInternal;
	}
	
}
		
