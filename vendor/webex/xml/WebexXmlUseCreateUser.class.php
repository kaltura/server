<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseCreateUser extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlUseSalesCenterType
	 */
	protected $salesCenter;
	
	/**
	 *
	 * @var boolean
	 */
	protected $syncWebOffice;
	
	/**
	 *
	 * @var boolean
	 */
	protected $sendWelcome;
	
	/**
	 *
	 * @var boolean
	 */
	protected $validateFormat;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'salesCenter':
				return 'WebexXmlUseSalesCenterType';
	
			case 'syncWebOffice':
				return 'boolean';
	
			case 'sendWelcome':
				return 'boolean';
	
			case 'validateFormat':
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
			'salesCenter',
			'syncWebOffice',
			'sendWelcome',
			'validateFormat',
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
		return 'createUser';
	}
	
	/**
	 * @param WebexXmlUseSalesCenterType $salesCenter
	 */
	public function setSalesCenter(WebexXmlUseSalesCenterType $salesCenter)
	{
		$this->salesCenter = $salesCenter;
	}
	
	/**
	 * @return WebexXmlUseSalesCenterType $salesCenter
	 */
	public function getSalesCenter()
	{
		return $this->salesCenter;
	}
	
	/**
	 * @param boolean $syncWebOffice
	 */
	public function setSyncWebOffice($syncWebOffice)
	{
		$this->syncWebOffice = $syncWebOffice;
	}
	
	/**
	 * @return boolean $syncWebOffice
	 */
	public function getSyncWebOffice()
	{
		return $this->syncWebOffice;
	}
	
	/**
	 * @param boolean $sendWelcome
	 */
	public function setSendWelcome($sendWelcome)
	{
		$this->sendWelcome = $sendWelcome;
	}
	
	/**
	 * @return boolean $sendWelcome
	 */
	public function getSendWelcome()
	{
		return $this->sendWelcome;
	}
	
	/**
	 * @param boolean $validateFormat
	 */
	public function setValidateFormat($validateFormat)
	{
		$this->validateFormat = $validateFormat;
	}
	
	/**
	 * @return boolean $validateFormat
	 */
	public function getValidateFormat()
	{
		return $this->validateFormat;
	}
	
}
		
