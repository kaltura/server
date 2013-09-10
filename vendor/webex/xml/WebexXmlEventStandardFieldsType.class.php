<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventStandardFieldsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldType
	 */
	protected $phone;
	
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldType
	 */
	protected $company;
	
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldType
	 */
	protected $title;
	
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldType
	 */
	protected $numEmployees;
	
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldType
	 */
	protected $futureInfo;
	
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldType
	 */
	protected $address1;
	
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldType
	 */
	protected $address2;
	
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldType
	 */
	protected $city;
	
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldType
	 */
	protected $state;
	
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldType
	 */
	protected $postalCode;
	
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldType
	 */
	protected $country;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'phone':
				return 'WebexXmlEventEnrollmentFieldType';
	
			case 'company':
				return 'WebexXmlEventEnrollmentFieldType';
	
			case 'title':
				return 'WebexXmlEventEnrollmentFieldType';
	
			case 'numEmployees':
				return 'WebexXmlEventEnrollmentFieldType';
	
			case 'futureInfo':
				return 'WebexXmlEventEnrollmentFieldType';
	
			case 'address1':
				return 'WebexXmlEventEnrollmentFieldType';
	
			case 'address2':
				return 'WebexXmlEventEnrollmentFieldType';
	
			case 'city':
				return 'WebexXmlEventEnrollmentFieldType';
	
			case 'state':
				return 'WebexXmlEventEnrollmentFieldType';
	
			case 'postalCode':
				return 'WebexXmlEventEnrollmentFieldType';
	
			case 'country':
				return 'WebexXmlEventEnrollmentFieldType';
	
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
			'company',
			'title',
			'numEmployees',
			'futureInfo',
			'address1',
			'address2',
			'city',
			'state',
			'postalCode',
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
		return 'standardFieldsType';
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldType $phone
	 */
	public function setPhone(WebexXmlEventEnrollmentFieldType $phone)
	{
		$this->phone = $phone;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldType $phone
	 */
	public function getPhone()
	{
		return $this->phone;
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldType $company
	 */
	public function setCompany(WebexXmlEventEnrollmentFieldType $company)
	{
		$this->company = $company;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldType $company
	 */
	public function getCompany()
	{
		return $this->company;
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldType $title
	 */
	public function setTitle(WebexXmlEventEnrollmentFieldType $title)
	{
		$this->title = $title;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldType $title
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldType $numEmployees
	 */
	public function setNumEmployees(WebexXmlEventEnrollmentFieldType $numEmployees)
	{
		$this->numEmployees = $numEmployees;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldType $numEmployees
	 */
	public function getNumEmployees()
	{
		return $this->numEmployees;
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldType $futureInfo
	 */
	public function setFutureInfo(WebexXmlEventEnrollmentFieldType $futureInfo)
	{
		$this->futureInfo = $futureInfo;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldType $futureInfo
	 */
	public function getFutureInfo()
	{
		return $this->futureInfo;
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldType $address1
	 */
	public function setAddress1(WebexXmlEventEnrollmentFieldType $address1)
	{
		$this->address1 = $address1;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldType $address1
	 */
	public function getAddress1()
	{
		return $this->address1;
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldType $address2
	 */
	public function setAddress2(WebexXmlEventEnrollmentFieldType $address2)
	{
		$this->address2 = $address2;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldType $address2
	 */
	public function getAddress2()
	{
		return $this->address2;
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldType $city
	 */
	public function setCity(WebexXmlEventEnrollmentFieldType $city)
	{
		$this->city = $city;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldType $city
	 */
	public function getCity()
	{
		return $this->city;
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldType $state
	 */
	public function setState(WebexXmlEventEnrollmentFieldType $state)
	{
		$this->state = $state;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldType $state
	 */
	public function getState()
	{
		return $this->state;
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldType $postalCode
	 */
	public function setPostalCode(WebexXmlEventEnrollmentFieldType $postalCode)
	{
		$this->postalCode = $postalCode;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldType $postalCode
	 */
	public function getPostalCode()
	{
		return $this->postalCode;
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldType $country
	 */
	public function setCountry(WebexXmlEventEnrollmentFieldType $country)
	{
		$this->country = $country;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldType $country
	 */
	public function getCountry()
	{
		return $this->country;
	}
	
}
		
