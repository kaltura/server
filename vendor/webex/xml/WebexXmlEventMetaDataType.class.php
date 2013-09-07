<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventMetaDataType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $sessionName;
	
	/**
	 *
	 * @var integer
	 */
	protected $sessionType;
	
	/**
	 *
	 * @var string
	 */
	protected $description;
	
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
	 * @var integer
	 */
	protected $programID;
	
	/**
	 *
	 * @var WebexXmlEventPostEventSurveyType
	 */
	protected $postEventSurvey;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'sessionName':
				return 'string';
	
			case 'sessionType':
				return 'integer';
	
			case 'description':
				return 'string';
	
			case 'defaultHighestMT':
				return 'boolean';
	
			case 'sessionTemplate':
				return 'WebexXmlComSessionTemplateType';
	
			case 'programID':
				return 'integer';
	
			case 'postEventSurvey':
				return 'WebexXmlEventPostEventSurveyType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'sessionName',
			'sessionType',
			'description',
			'defaultHighestMT',
			'sessionTemplate',
			'programID',
			'postEventSurvey',
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
	 * @param string $sessionName
	 */
	public function setSessionName($sessionName)
	{
		$this->sessionName = $sessionName;
	}
	
	/**
	 * @return string $sessionName
	 */
	public function getSessionName()
	{
		return $this->sessionName;
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
	 * @param integer $programID
	 */
	public function setProgramID($programID)
	{
		$this->programID = $programID;
	}
	
	/**
	 * @return integer $programID
	 */
	public function getProgramID()
	{
		return $this->programID;
	}
	
	/**
	 * @param WebexXmlEventPostEventSurveyType $postEventSurvey
	 */
	public function setPostEventSurvey(WebexXmlEventPostEventSurveyType $postEventSurvey)
	{
		$this->postEventSurvey = $postEventSurvey;
	}
	
	/**
	 * @return WebexXmlEventPostEventSurveyType $postEventSurvey
	 */
	public function getPostEventSurvey()
	{
		return $this->postEventSurvey;
	}
	
}
		
