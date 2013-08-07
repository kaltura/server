<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSalesMetaDataType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $confName;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $agenda;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $account;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $opportunity;
	
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
	 * @var integer
	 */
	protected $intAccountID;
	
	/**
	 *
	 * @var integer
	 */
	protected $intOpptyID;
	
	/**
	 *
	 * @var integer
	 */
	protected $extSystemID;
	
	/**
	 *
	 * @var WebexXmlComSessionTemplateType
	 */
	protected $sessionTemplate;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'confName':
				return 'WebexXml';
	
			case 'agenda':
				return 'WebexXml';
	
			case 'account':
				return 'WebexXml';
	
			case 'opportunity':
				return 'WebexXml';
	
			case 'sessionType':
				return 'integer';
	
			case 'defaultHighestMT':
				return 'boolean';
	
			case 'intAccountID':
				return 'integer';
	
			case 'intOpptyID':
				return 'integer';
	
			case 'extSystemID':
				return 'integer';
	
			case 'sessionTemplate':
				return 'WebexXmlComSessionTemplateType';
	
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
			'agenda',
			'account',
			'opportunity',
			'sessionType',
			'defaultHighestMT',
			'intAccountID',
			'intOpptyID',
			'extSystemID',
			'sessionTemplate',
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
	 * @param WebexXml $confName
	 */
	public function setConfName(WebexXml $confName)
	{
		$this->confName = $confName;
	}
	
	/**
	 * @return WebexXml $confName
	 */
	public function getConfName()
	{
		return $this->confName;
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
	 * @param WebexXml $account
	 */
	public function setAccount(WebexXml $account)
	{
		$this->account = $account;
	}
	
	/**
	 * @return WebexXml $account
	 */
	public function getAccount()
	{
		return $this->account;
	}
	
	/**
	 * @param WebexXml $opportunity
	 */
	public function setOpportunity(WebexXml $opportunity)
	{
		$this->opportunity = $opportunity;
	}
	
	/**
	 * @return WebexXml $opportunity
	 */
	public function getOpportunity()
	{
		return $this->opportunity;
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
	 * @param integer $intAccountID
	 */
	public function setIntAccountID($intAccountID)
	{
		$this->intAccountID = $intAccountID;
	}
	
	/**
	 * @return integer $intAccountID
	 */
	public function getIntAccountID()
	{
		return $this->intAccountID;
	}
	
	/**
	 * @param integer $intOpptyID
	 */
	public function setIntOpptyID($intOpptyID)
	{
		$this->intOpptyID = $intOpptyID;
	}
	
	/**
	 * @return integer $intOpptyID
	 */
	public function getIntOpptyID()
	{
		return $this->intOpptyID;
	}
	
	/**
	 * @param integer $extSystemID
	 */
	public function setExtSystemID($extSystemID)
	{
		$this->extSystemID = $extSystemID;
	}
	
	/**
	 * @return integer $extSystemID
	 */
	public function getExtSystemID()
	{
		return $this->extSystemID;
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
	
}
		
