<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlServGlobalCallInNumType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $countryAlias;
	
	/**
	 *
	 * @var string
	 */
	protected $phoneNumber;
	
	/**
	 *
	 * @var boolean
	 */
	protected $tollFree;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'countryAlias':
				return 'string';
	
			case 'phoneNumber':
				return 'string';
	
			case 'tollFree':
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
			'countryAlias',
			'phoneNumber',
			'tollFree',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'countryAlias',
			'phoneNumber',
			'tollFree',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'globalCallInNumType';
	}
	
	/**
	 * @param string $countryAlias
	 */
	public function setCountryAlias($countryAlias)
	{
		$this->countryAlias = $countryAlias;
	}
	
	/**
	 * @return string $countryAlias
	 */
	public function getCountryAlias()
	{
		return $this->countryAlias;
	}
	
	/**
	 * @param string $phoneNumber
	 */
	public function setPhoneNumber($phoneNumber)
	{
		$this->phoneNumber = $phoneNumber;
	}
	
	/**
	 * @return string $phoneNumber
	 */
	public function getPhoneNumber()
	{
		return $this->phoneNumber;
	}
	
	/**
	 * @param boolean $tollFree
	 */
	public function setTollFree($tollFree)
	{
		$this->tollFree = $tollFree;
	}
	
	/**
	 * @return boolean $tollFree
	 */
	public function getTollFree()
	{
		return $this->tollFree;
	}
	
}
		
