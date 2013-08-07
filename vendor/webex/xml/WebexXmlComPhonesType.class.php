<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlComPhonesType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $phone;
	
	/**
	 *
	 * @var string
	 */
	protected $mobilePhone;
	
	/**
	 *
	 * @var string
	 */
	protected $fax;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'phone':
				return 'string';
	
			case 'mobilePhone':
				return 'string';
	
			case 'fax':
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
			'phone',
			'mobilePhone',
			'fax',
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
		return 'phonesType';
	}
	
	/**
	 * @param string $phone
	 */
	public function setPhone($phone)
	{
		$this->phone = $phone;
	}
	
	/**
	 * @return string $phone
	 */
	public function getPhone()
	{
		return $this->phone;
	}
	
	/**
	 * @param string $mobilePhone
	 */
	public function setMobilePhone($mobilePhone)
	{
		$this->mobilePhone = $mobilePhone;
	}
	
	/**
	 * @return string $mobilePhone
	 */
	public function getMobilePhone()
	{
		return $this->mobilePhone;
	}
	
	/**
	 * @param string $fax
	 */
	public function setFax($fax)
	{
		$this->fax = $fax;
	}
	
	/**
	 * @return string $fax
	 */
	public function getFax()
	{
		return $this->fax;
	}
	
}
		
