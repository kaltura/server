<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlServCallInNumType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $tollNum;
	
	/**
	 *
	 * @var string
	 */
	protected $tollFreeNum;
	
	/**
	 *
	 * @var string
	 */
	protected $intlLocalNum;
	
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
			case 'tollNum':
				return 'string';
	
			case 'tollFreeNum':
				return 'string';
	
			case 'intlLocalNum':
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
			'tollNum',
			'tollFreeNum',
			'intlLocalNum',
			'globalNum',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'tollNum',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'callInNumType';
	}
	
	/**
	 * @param string $tollNum
	 */
	public function setTollNum($tollNum)
	{
		$this->tollNum = $tollNum;
	}
	
	/**
	 * @return string $tollNum
	 */
	public function getTollNum()
	{
		return $this->tollNum;
	}
	
	/**
	 * @param string $tollFreeNum
	 */
	public function setTollFreeNum($tollFreeNum)
	{
		$this->tollFreeNum = $tollFreeNum;
	}
	
	/**
	 * @return string $tollFreeNum
	 */
	public function getTollFreeNum()
	{
		return $this->tollFreeNum;
	}
	
	/**
	 * @param string $intlLocalNum
	 */
	public function setIntlLocalNum($intlLocalNum)
	{
		$this->intlLocalNum = $intlLocalNum;
	}
	
	/**
	 * @return string $intlLocalNum
	 */
	public function getIntlLocalNum()
	{
		return $this->intlLocalNum;
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
		
