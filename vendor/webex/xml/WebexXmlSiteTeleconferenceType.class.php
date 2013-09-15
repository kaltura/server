<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteTeleconferenceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlSiteTelephonySupportType
	 */
	protected $telephonySupport;
	
	/**
	 *
	 * @var boolean
	 */
	protected $tollFree;
	
	/**
	 *
	 * @var boolean
	 */
	protected $intlLocalCallIn;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'telephonySupport':
				return 'WebexXmlSiteTelephonySupportType';
	
			case 'tollFree':
				return 'boolean';
	
			case 'intlLocalCallIn':
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
			'telephonySupport',
			'tollFree',
			'intlLocalCallIn',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'telephonySupport',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'teleconferenceType';
	}
	
	/**
	 * @param WebexXmlSiteTelephonySupportType $telephonySupport
	 */
	public function setTelephonySupport(WebexXmlSiteTelephonySupportType $telephonySupport)
	{
		$this->telephonySupport = $telephonySupport;
	}
	
	/**
	 * @return WebexXmlSiteTelephonySupportType $telephonySupport
	 */
	public function getTelephonySupport()
	{
		return $this->telephonySupport;
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
	
}
		
