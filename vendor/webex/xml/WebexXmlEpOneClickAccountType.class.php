<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpOneClickAccountType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $participantLimitedAccessCode;
	
	/**
	 *
	 * @var string
	 */
	protected $intlLocalCallInNumber;
	
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
	 * @var WebexXmlArray<WebexXmlServGlobalCallInNumType>
	 */
	protected $globalNum;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'participantLimitedAccessCode':
				return 'string';
	
			case 'intlLocalCallInNumber':
				return 'string';
	
			case 'tollFreeCallInData':
				return 'string';
	
			case 'tollCallInData':
				return 'string';
	
			case 'globalNum':
				return 'WebexXmlArray<WebexXmlServGlobalCallInNumType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'participantLimitedAccessCode',
			'intlLocalCallInNumber',
			'tollFreeCallInData',
			'tollCallInData',
			'globalNum',
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
		return 'oneClickAccountType';
	}
	
	/**
	 * @param string $participantLimitedAccessCode
	 */
	public function setParticipantLimitedAccessCode($participantLimitedAccessCode)
	{
		$this->participantLimitedAccessCode = $participantLimitedAccessCode;
	}
	
	/**
	 * @return string $participantLimitedAccessCode
	 */
	public function getParticipantLimitedAccessCode()
	{
		return $this->participantLimitedAccessCode;
	}
	
	/**
	 * @param string $intlLocalCallInNumber
	 */
	public function setIntlLocalCallInNumber($intlLocalCallInNumber)
	{
		$this->intlLocalCallInNumber = $intlLocalCallInNumber;
	}
	
	/**
	 * @return string $intlLocalCallInNumber
	 */
	public function getIntlLocalCallInNumber()
	{
		return $this->intlLocalCallInNumber;
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
	 * @param WebexXmlArray<WebexXmlServGlobalCallInNumType> $globalNum
	 */
	public function setGlobalNum(WebexXmlArray $globalNum)
	{
		if($globalNum->getType() != 'WebexXmlServGlobalCallInNumType')
			throw new WebexXmlException(get_class($this) . "::globalNum must be of type WebexXmlServGlobalCallInNumType");
		
		$this->globalNum = $globalNum;
	}
	
	/**
	 * @return WebexXmlArray $globalNum
	 */
	public function getGlobalNum()
	{
		return $this->globalNum;
	}
	
}
		
