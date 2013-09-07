<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseThirdPartyAccountType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $name;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $confBridgeNum;
	
	/**
	 *
	 * @var integer
	 */
	protected $pause;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $accessCode;
	
	/**
	 *
	 * @var integer
	 */
	protected $accountIndex;
	
	/**
	 *
	 * @var boolean
	 */
	protected $defaultFlag;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $tollFreeNum;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $moderatorCode;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $phoneName1;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $phoneName2;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'name':
				return 'WebexXml';
	
			case 'confBridgeNum':
				return 'WebexXml';
	
			case 'pause':
				return 'integer';
	
			case 'accessCode':
				return 'WebexXml';
	
			case 'accountIndex':
				return 'integer';
	
			case 'defaultFlag':
				return 'boolean';
	
			case 'tollFreeNum':
				return 'WebexXml';
	
			case 'moderatorCode':
				return 'WebexXml';
	
			case 'phoneName1':
				return 'WebexXml';
	
			case 'phoneName2':
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
			'name',
			'confBridgeNum',
			'pause',
			'accessCode',
			'accountIndex',
			'defaultFlag',
			'tollFreeNum',
			'moderatorCode',
			'phoneName1',
			'phoneName2',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'accountIndex',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'thirdPartyAccountType';
	}
	
	/**
	 * @param WebexXml $name
	 */
	public function setName(WebexXml $name)
	{
		$this->name = $name;
	}
	
	/**
	 * @return WebexXml $name
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * @param WebexXml $confBridgeNum
	 */
	public function setConfBridgeNum(WebexXml $confBridgeNum)
	{
		$this->confBridgeNum = $confBridgeNum;
	}
	
	/**
	 * @return WebexXml $confBridgeNum
	 */
	public function getConfBridgeNum()
	{
		return $this->confBridgeNum;
	}
	
	/**
	 * @param integer $pause
	 */
	public function setPause($pause)
	{
		$this->pause = $pause;
	}
	
	/**
	 * @return integer $pause
	 */
	public function getPause()
	{
		return $this->pause;
	}
	
	/**
	 * @param WebexXml $accessCode
	 */
	public function setAccessCode(WebexXml $accessCode)
	{
		$this->accessCode = $accessCode;
	}
	
	/**
	 * @return WebexXml $accessCode
	 */
	public function getAccessCode()
	{
		return $this->accessCode;
	}
	
	/**
	 * @param integer $accountIndex
	 */
	public function setAccountIndex($accountIndex)
	{
		$this->accountIndex = $accountIndex;
	}
	
	/**
	 * @return integer $accountIndex
	 */
	public function getAccountIndex()
	{
		return $this->accountIndex;
	}
	
	/**
	 * @param boolean $defaultFlag
	 */
	public function setDefaultFlag($defaultFlag)
	{
		$this->defaultFlag = $defaultFlag;
	}
	
	/**
	 * @return boolean $defaultFlag
	 */
	public function getDefaultFlag()
	{
		return $this->defaultFlag;
	}
	
	/**
	 * @param WebexXml $tollFreeNum
	 */
	public function setTollFreeNum(WebexXml $tollFreeNum)
	{
		$this->tollFreeNum = $tollFreeNum;
	}
	
	/**
	 * @return WebexXml $tollFreeNum
	 */
	public function getTollFreeNum()
	{
		return $this->tollFreeNum;
	}
	
	/**
	 * @param WebexXml $moderatorCode
	 */
	public function setModeratorCode(WebexXml $moderatorCode)
	{
		$this->moderatorCode = $moderatorCode;
	}
	
	/**
	 * @return WebexXml $moderatorCode
	 */
	public function getModeratorCode()
	{
		return $this->moderatorCode;
	}
	
	/**
	 * @param WebexXml $phoneName1
	 */
	public function setPhoneName1(WebexXml $phoneName1)
	{
		$this->phoneName1 = $phoneName1;
	}
	
	/**
	 * @return WebexXml $phoneName1
	 */
	public function getPhoneName1()
	{
		return $this->phoneName1;
	}
	
	/**
	 * @param WebexXml $phoneName2
	 */
	public function setPhoneName2(WebexXml $phoneName2)
	{
		$this->phoneName2 = $phoneName2;
	}
	
	/**
	 * @return WebexXml $phoneName2
	 */
	public function getPhoneName2()
	{
		return $this->phoneName2;
	}
	
}

