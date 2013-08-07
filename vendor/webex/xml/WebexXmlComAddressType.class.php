<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlComAddressType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlComAddressTypeType
	 */
	protected $addressType;
	
	/**
	 *
	 * @var string
	 */
	protected $address1;
	
	/**
	 *
	 * @var string
	 */
	protected $address2;
	
	/**
	 *
	 * @var string
	 */
	protected $city;
	
	/**
	 *
	 * @var string
	 */
	protected $state;
	
	/**
	 *
	 * @var string
	 */
	protected $zipCode;
	
	/**
	 *
	 * @var string
	 */
	protected $country;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'addressType':
				return 'WebexXmlComAddressTypeType';
	
			case 'address1':
				return 'string';
	
			case 'address2':
				return 'string';
	
			case 'city':
				return 'string';
	
			case 'state':
				return 'string';
	
			case 'zipCode':
				return 'string';
	
			case 'country':
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
			'addressType',
			'address1',
			'address2',
			'city',
			'state',
			'zipCode',
			'country',
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
		return 'addressType';
	}
	
	/**
	 * @param WebexXmlComAddressTypeType $addressType
	 */
	public function setAddressType(WebexXmlComAddressTypeType $addressType)
	{
		$this->addressType = $addressType;
	}
	
	/**
	 * @return WebexXmlComAddressTypeType $addressType
	 */
	public function getAddressType()
	{
		return $this->addressType;
	}
	
	/**
	 * @param string $address1
	 */
	public function setAddress1($address1)
	{
		$this->address1 = $address1;
	}
	
	/**
	 * @return string $address1
	 */
	public function getAddress1()
	{
		return $this->address1;
	}
	
	/**
	 * @param string $address2
	 */
	public function setAddress2($address2)
	{
		$this->address2 = $address2;
	}
	
	/**
	 * @return string $address2
	 */
	public function getAddress2()
	{
		return $this->address2;
	}
	
	/**
	 * @param string $city
	 */
	public function setCity($city)
	{
		$this->city = $city;
	}
	
	/**
	 * @return string $city
	 */
	public function getCity()
	{
		return $this->city;
	}
	
	/**
	 * @param string $state
	 */
	public function setState($state)
	{
		$this->state = $state;
	}
	
	/**
	 * @return string $state
	 */
	public function getState()
	{
		return $this->state;
	}
	
	/**
	 * @param string $zipCode
	 */
	public function setZipCode($zipCode)
	{
		$this->zipCode = $zipCode;
	}
	
	/**
	 * @return string $zipCode
	 */
	public function getZipCode()
	{
		return $this->zipCode;
	}
	
	/**
	 * @param string $country
	 */
	public function setCountry($country)
	{
		$this->country = $country;
	}
	
	/**
	 * @return string $country
	 */
	public function getCountry()
	{
		return $this->country;
	}
	
}
		
