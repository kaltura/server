<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseTspAccountType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $tollFreeCallInNumber;
	
	/**
	 *
	 * @var int
	 */
	protected $accountIndex;
	
	/**
	 *
	 * @var string
	 */
	protected $tollCallInNumber;
	
	/**
	 *
	 * @var string
	 */
	protected $subscriberAccessCode;
	
	/**
	 *
	 * @var string
	 */
	protected $participantAccessCode;
	
	/**
	 *
	 * @var boolean
	 */
	protected $createOnBridge;
	
	/**
	 *
	 * @var boolean
	 */
	protected $defaultFlag;
	
	/**
	 *
	 * @var string
	 */
	protected $custom1;
	
	/**
	 *
	 * @var string
	 */
	protected $custom2;
	
	/**
	 *
	 * @var string
	 */
	protected $custom3;
	
	/**
	 *
	 * @var string
	 */
	protected $tollFreeCallInData;
	
	/**
	 *
	 * @var string
	 */
	protected $tollCallInData;
	
	/**
	 *
	 * @var WebexXmlUseNbrDialOutType
	 */
	protected $nbrDialOut;
	
	/**
	 *
	 * @var boolean
	 */
	protected $delete;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'tollFreeCallInNumber':
				return 'string';
	
			case 'accountIndex':
				return 'int';
	
			case 'tollCallInNumber':
				return 'string';
	
			case 'subscriberAccessCode':
				return 'string';
	
			case 'participantAccessCode':
				return 'string';
	
			case 'createOnBridge':
				return 'boolean';
	
			case 'defaultFlag':
				return 'boolean';
	
			case 'custom1':
				return 'string';
	
			case 'custom2':
				return 'string';
	
			case 'custom3':
				return 'string';
	
			case 'tollFreeCallInData':
				return 'string';
	
			case 'tollCallInData':
				return 'string';
	
			case 'nbrDialOut':
				return 'WebexXmlUseNbrDialOutType';
	
			case 'delete':
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
			'tollFreeCallInNumber',
			'accountIndex',
			'tollCallInNumber',
			'subscriberAccessCode',
			'participantAccessCode',
			'createOnBridge',
			'defaultFlag',
			'custom1',
			'custom2',
			'custom3',
			'tollFreeCallInData',
			'tollCallInData',
			'nbrDialOut',
			'delete',
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
		return 'tspAccountType';
	}
	
	/**
	 * @param string $tollFreeCallInNumber
	 */
	public function setTollFreeCallInNumber($tollFreeCallInNumber)
	{
		$this->tollFreeCallInNumber = $tollFreeCallInNumber;
	}
	
	/**
	 * @return string $tollFreeCallInNumber
	 */
	public function getTollFreeCallInNumber()
	{
		return $this->tollFreeCallInNumber;
	}
	
	/**
	 * @param int $accountIndex
	 */
	public function setAccountIndex($accountIndex)
	{
		$this->accountIndex = $accountIndex;
	}
	
	/**
	 * @return int $accountIndex
	 */
	public function getAccountIndex()
	{
		return $this->accountIndex;
	}
	
	/**
	 * @param string $tollCallInNumber
	 */
	public function setTollCallInNumber($tollCallInNumber)
	{
		$this->tollCallInNumber = $tollCallInNumber;
	}
	
	/**
	 * @return string $tollCallInNumber
	 */
	public function getTollCallInNumber()
	{
		return $this->tollCallInNumber;
	}
	
	/**
	 * @param string $subscriberAccessCode
	 */
	public function setSubscriberAccessCode($subscriberAccessCode)
	{
		$this->subscriberAccessCode = $subscriberAccessCode;
	}
	
	/**
	 * @return string $subscriberAccessCode
	 */
	public function getSubscriberAccessCode()
	{
		return $this->subscriberAccessCode;
	}
	
	/**
	 * @param string $participantAccessCode
	 */
	public function setParticipantAccessCode($participantAccessCode)
	{
		$this->participantAccessCode = $participantAccessCode;
	}
	
	/**
	 * @return string $participantAccessCode
	 */
	public function getParticipantAccessCode()
	{
		return $this->participantAccessCode;
	}
	
	/**
	 * @param boolean $createOnBridge
	 */
	public function setCreateOnBridge($createOnBridge)
	{
		$this->createOnBridge = $createOnBridge;
	}
	
	/**
	 * @return boolean $createOnBridge
	 */
	public function getCreateOnBridge()
	{
		return $this->createOnBridge;
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
	 * @param string $custom1
	 */
	public function setCustom1($custom1)
	{
		$this->custom1 = $custom1;
	}
	
	/**
	 * @return string $custom1
	 */
	public function getCustom1()
	{
		return $this->custom1;
	}
	
	/**
	 * @param string $custom2
	 */
	public function setCustom2($custom2)
	{
		$this->custom2 = $custom2;
	}
	
	/**
	 * @return string $custom2
	 */
	public function getCustom2()
	{
		return $this->custom2;
	}
	
	/**
	 * @param string $custom3
	 */
	public function setCustom3($custom3)
	{
		$this->custom3 = $custom3;
	}
	
	/**
	 * @return string $custom3
	 */
	public function getCustom3()
	{
		return $this->custom3;
	}
	
	/**
	 * @param string $tollFreeCallInData
	 */
	public function setTollFreeCallInData($tollFreeCallInData)
	{
		$this->tollFreeCallInData = $tollFreeCallInData;
	}
	
	/**
	 * @return string $tollFreeCallInData
	 */
	public function getTollFreeCallInData()
	{
		return $this->tollFreeCallInData;
	}
	
	/**
	 * @param string $tollCallInData
	 */
	public function setTollCallInData($tollCallInData)
	{
		$this->tollCallInData = $tollCallInData;
	}
	
	/**
	 * @return string $tollCallInData
	 */
	public function getTollCallInData()
	{
		return $this->tollCallInData;
	}
	
	/**
	 * @param WebexXmlUseNbrDialOutType $nbrDialOut
	 */
	public function setNbrDialOut(WebexXmlUseNbrDialOutType $nbrDialOut)
	{
		$this->nbrDialOut = $nbrDialOut;
	}
	
	/**
	 * @return WebexXmlUseNbrDialOutType $nbrDialOut
	 */
	public function getNbrDialOut()
	{
		return $this->nbrDialOut;
	}
	
	/**
	 * @param boolean $delete
	 */
	public function setDelete($delete)
	{
		$this->delete = $delete;
	}
	
	/**
	 * @return boolean $delete
	 */
	public function getDelete()
	{
		return $this->delete;
	}
	
}
		
