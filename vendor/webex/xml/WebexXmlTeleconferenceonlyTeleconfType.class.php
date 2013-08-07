<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTeleconferenceonlyTeleconfType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $requestConferenceNumber;
	
	/**
	 *
	 * @var integer
	 */
	protected $accountIndex;
	
	/**
	 *
	 * @var string
	 */
	protected $extTelephonyDescription;
	
	/**
	 *
	 * @var boolean
	 */
	protected $intlLocalCallIn;
	
	/**
	 *
	 * @var string
	 */
	protected $teleconfLocation;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'requestConferenceNumber':
				return 'boolean';
	
			case 'accountIndex':
				return 'integer';
	
			case 'extTelephonyDescription':
				return 'string';
	
			case 'intlLocalCallIn':
				return 'boolean';
	
			case 'teleconfLocation':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'requestConferenceNumber',
			'accountIndex',
			'extTelephonyDescription',
			'intlLocalCallIn',
			'teleconfLocation',
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
		return 'teleconfType';
	}
	
	/**
	 * @param boolean $requestConferenceNumber
	 */
	public function setRequestConferenceNumber($requestConferenceNumber)
	{
		$this->requestConferenceNumber = $requestConferenceNumber;
	}
	
	/**
	 * @return boolean $requestConferenceNumber
	 */
	public function getRequestConferenceNumber()
	{
		return $this->requestConferenceNumber;
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
	 * @param string $extTelephonyDescription
	 */
	public function setExtTelephonyDescription($extTelephonyDescription)
	{
		$this->extTelephonyDescription = $extTelephonyDescription;
	}
	
	/**
	 * @return string $extTelephonyDescription
	 */
	public function getExtTelephonyDescription()
	{
		return $this->extTelephonyDescription;
	}
	
	/**
	 * @param boolean $intlLocalCallIn
	 */
	public function setIntlLocalCallIn($intlLocalCallIn)
	{
		$this->intlLocalCallIn = $intlLocalCallIn;
	}
	
	/**
	 * @return boolean $intlLocalCallIn
	 */
	public function getIntlLocalCallIn()
	{
		return $this->intlLocalCallIn;
	}
	
	/**
	 * @param string $teleconfLocation
	 */
	public function setTeleconfLocation($teleconfLocation)
	{
		$this->teleconfLocation = $teleconfLocation;
	}
	
	/**
	 * @return string $teleconfLocation
	 */
	public function getTeleconfLocation()
	{
		return $this->teleconfLocation;
	}
	
}
		
