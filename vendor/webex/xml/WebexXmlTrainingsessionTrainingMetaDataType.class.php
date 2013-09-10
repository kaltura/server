<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTrainingsessionTrainingMetaDataType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $agenda;
	
	/**
	 *
	 * @var string
	 */
	protected $description;
	
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
	 * @var integer
	 */
	protected $sessionType;
	
	/**
	 *
	 * @var boolean
	 */
	protected $defaultHighestMT;
	
	/**
	 *
	 * @var WebexXmlComSessionTemplateType
	 */
	protected $sessionTemplate;
	
	/**
	 *
	 * @var boolean
	 */
	protected $enableGreeting;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'agenda':
				return 'WebexXml';
	
			case 'description':
				return 'string';
	
			case 'greeting':
				return 'string';
	
			case 'location':
				return 'string';
	
			case 'invitation':
				return 'string';
	
			case 'sessionType':
				return 'integer';
	
			case 'defaultHighestMT':
				return 'boolean';
	
			case 'sessionTemplate':
				return 'WebexXmlComSessionTemplateType';
	
			case 'enableGreeting':
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
			'agenda',
			'description',
			'greeting',
			'location',
			'invitation',
			'sessionType',
			'defaultHighestMT',
			'sessionTemplate',
			'enableGreeting',
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
		return 'trainingMetaDataType';
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
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	/**
	 * @return string $description
	 */
	public function getDescription()
	{
		return $this->description;
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
	 * @param integer $sessionType
	 */
	public function setSessionType($sessionType)
	{
		$this->sessionType = $sessionType;
	}
	
	/**
	 * @return integer $sessionType
	 */
	public function getSessionType()
	{
		return $this->sessionType;
	}
	
	/**
	 * @param boolean $defaultHighestMT
	 */
	public function setDefaultHighestMT($defaultHighestMT)
	{
		$this->defaultHighestMT = $defaultHighestMT;
	}
	
	/**
	 * @return boolean $defaultHighestMT
	 */
	public function getDefaultHighestMT()
	{
		return $this->defaultHighestMT;
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
	 * @param boolean $enableGreeting
	 */
	public function setEnableGreeting($enableGreeting)
	{
		$this->enableGreeting = $enableGreeting;
	}
	
	/**
	 * @return boolean $enableGreeting
	 */
	public function getEnableGreeting()
	{
		return $this->enableGreeting;
	}
	
}
		
