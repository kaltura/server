<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlMeetingStandardFieldsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlMeetRegistrationFieldType
	 */
	protected $phone;
	
	/**
	 *
	 * @var WebexXmlMeetRegistrationFieldType
	 */
	protected $title;
	
	/**
	 *
	 * @var WebexXmlMeetRegistrationFieldType
	 */
	protected $company;
	
	/**
	 *
	 * @var WebexXmlMeetRegistrationFieldType
	 */
	protected $address1;
	
	/**
	 *
	 * @var WebexXmlMeetRegistrationFieldType
	 */
	protected $address2;
	
	/**
	 *
	 * @var WebexXmlMeetRegistrationFieldType
	 */
	protected $city;
	
	/**
	 *
	 * @var WebexXmlMeetRegistrationFieldType
	 */
	protected $state;
	
	/**
	 *
	 * @var WebexXmlMeetRegistrationFieldType
	 */
	protected $postalCode;
	
	/**
	 *
	 * @var WebexXmlMeetRegistrationFieldType
	 */
	protected $country;
	
	/**
	 *
	 * @var WebexXmlMeetRegistrationFieldType
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
				return 'WebexXmlMeetRegistrationFieldType';
	
			case 'title':
				return 'WebexXmlMeetRegistrationFieldType';
	
			case 'company':
				return 'WebexXmlMeetRegistrationFieldType';
	
			case 'address1':
				return 'WebexXmlMeetRegistrationFieldType';
	
			case 'address2':
				return 'WebexXmlMeetRegistrationFieldType';
	
			case 'city':
				return 'WebexXmlMeetRegistrationFieldType';
	
			case 'state':
				return 'WebexXmlMeetRegistrationFieldType';
	
			case 'postalCode':
				return 'WebexXmlMeetRegistrationFieldType';
	
			case 'country':
				return 'WebexXmlMeetRegistrationFieldType';
	
			case 'fax':
				return 'WebexXmlMeetRegistrationFieldType';
	
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
			'title',
			'company',
			'address1',
			'address2',
			'city',
			'state',
			'postalCode',
			'country',
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
		return 'standardFieldsType';
	}
	
	/**
	 * @param WebexXmlMeetRegistrationFieldType $phone
	 */
	public function setPhone(WebexXmlMeetRegistrationFieldType $phone)
	{
		$this->phone = $phone;
	}
	
	/**
	 * @return WebexXmlMeetRegistrationFieldType $phone
	 */
	public function getPhone()
	{
		return $this->phone;
	}
	
	/**
	 * @param WebexXmlMeetRegistrationFieldType $title
	 */
	public function setTitle(WebexXmlMeetRegistrationFieldType $title)
	{
		$this->title = $title;
	}
	
	/**
	 * @return WebexXmlMeetRegistrationFieldType $title
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	/**
	 * @param WebexXmlMeetRegistrationFieldType $company
	 */
	public function setCompany(WebexXmlMeetRegistrationFieldType $company)
	{
		$this->company = $company;
	}
	
	/**
	 * @return WebexXmlMeetRegistrationFieldType $company
	 */
	public function getCompany()
	{
		return $this->company;
	}
	
	/**
	 * @param WebexXmlMeetRegistrationFieldType $address1
	 */
	public function setAddress1(WebexXmlMeetRegistrationFieldType $address1)
	{
		$this->address1 = $address1;
	}
	
	/**
	 * @return WebexXmlMeetRegistrationFieldType $address1
	 */
	public function getAddress1()
	{
		return $this->address1;
	}
	
	/**
	 * @param WebexXmlMeetRegistrationFieldType $address2
	 */
	public function setAddress2(WebexXmlMeetRegistrationFieldType $address2)
	{
		$this->address2 = $address2;
	}
	
	/**
	 * @return WebexXmlMeetRegistrationFieldType $address2
	 */
	public function getAddress2()
	{
		return $this->address2;
	}
	
	/**
	 * @param WebexXmlMeetRegistrationFieldType $city
	 */
	public function setCity(WebexXmlMeetRegistrationFieldType $city)
	{
		$this->city = $city;
	}
	
	/**
	 * @return WebexXmlMeetRegistrationFieldType $city
	 */
	public function getCity()
	{
		return $this->city;
	}
	
	/**
	 * @param WebexXmlMeetRegistrationFieldType $state
	 */
	public function setState(WebexXmlMeetRegistrationFieldType $state)
	{
		$this->state = $state;
	}
	
	/**
	 * @return WebexXmlMeetRegistrationFieldType $state
	 */
	public function getState()
	{
		return $this->state;
	}
	
	/**
	 * @param WebexXmlMeetRegistrationFieldType $postalCode
	 */
	public function setPostalCode(WebexXmlMeetRegistrationFieldType $postalCode)
	{
		$this->postalCode = $postalCode;
	}
	
	/**
	 * @return WebexXmlMeetRegistrationFieldType $postalCode
	 */
	public function getPostalCode()
	{
		return $this->postalCode;
	}
	
	/**
	 * @param WebexXmlMeetRegistrationFieldType $country
	 */
	public function setCountry(WebexXmlMeetRegistrationFieldType $country)
	{
		$this->country = $country;
	}
	
	/**
	 * @return WebexXmlMeetRegistrationFieldType $country
	 */
	public function getCountry()
	{
		return $this->country;
	}
	
	/**
	 * @param WebexXmlMeetRegistrationFieldType $fax
	 */
	public function setFax(WebexXmlMeetRegistrationFieldType $fax)
	{
		$this->fax = $fax;
	}
	
	/**
	 * @return WebexXmlMeetRegistrationFieldType $fax
	 */
	public function getFax()
	{
		return $this->fax;
	}
	
}
		
