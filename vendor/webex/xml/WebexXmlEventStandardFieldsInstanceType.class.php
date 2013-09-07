<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventStandardFieldsInstanceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldInstanceType
	 */
	protected $firstName;
	
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldInstanceType
	 */
	protected $lastName;
	
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldInstanceType
	 */
	protected $emailAddress;
	
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldInstanceType
	 */
	protected $phone;
	
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldInstanceType
	 */
	protected $company;
	
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldInstanceType
	 */
	protected $title;
	
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldInstanceType
	 */
	protected $numEmployees;
	
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldInstanceType
	 */
	protected $futureInfo;
	
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldInstanceType
	 */
	protected $address1;
	
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldInstanceType
	 */
	protected $address2;
	
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldInstanceType
	 */
	protected $city;
	
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldInstanceType
	 */
	protected $state;
	
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldInstanceType
	 */
	protected $postalCode;
	
	/**
	 *
	 * @var WebexXmlEventEnrollmentFieldInstanceType
	 */
	protected $country;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'firstName':
				return 'WebexXmlEventEnrollmentFieldInstanceType';
	
			case 'lastName':
				return 'WebexXmlEventEnrollmentFieldInstanceType';
	
			case 'emailAddress':
				return 'WebexXmlEventEnrollmentFieldInstanceType';
	
			case 'phone':
				return 'WebexXmlEventEnrollmentFieldInstanceType';
	
			case 'company':
				return 'WebexXmlEventEnrollmentFieldInstanceType';
	
			case 'title':
				return 'WebexXmlEventEnrollmentFieldInstanceType';
	
			case 'numEmployees':
				return 'WebexXmlEventEnrollmentFieldInstanceType';
	
			case 'futureInfo':
				return 'WebexXmlEventEnrollmentFieldInstanceType';
	
			case 'address1':
				return 'WebexXmlEventEnrollmentFieldInstanceType';
	
			case 'address2':
				return 'WebexXmlEventEnrollmentFieldInstanceType';
	
			case 'city':
				return 'WebexXmlEventEnrollmentFieldInstanceType';
	
			case 'state':
				return 'WebexXmlEventEnrollmentFieldInstanceType';
	
			case 'postalCode':
				return 'WebexXmlEventEnrollmentFieldInstanceType';
	
			case 'country':
				return 'WebexXmlEventEnrollmentFieldInstanceType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'firstName',
			'lastName',
			'emailAddress',
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
			'firstName',
			'lastName',
			'emailAddress',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'standardFieldsInstanceType';
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldInstanceType $firstName
	 */
	public function setFirstName(WebexXmlEventEnrollmentFieldInstanceType $firstName)
	{
		$this->firstName = $firstName;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldInstanceType $firstName
	 */
	public function getFirstName()
	{
		return $this->firstName;
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldInstanceType $lastName
	 */
	public function setLastName(WebexXmlEventEnrollmentFieldInstanceType $lastName)
	{
		$this->lastName = $lastName;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldInstanceType $lastName
	 */
	public function getLastName()
	{
		return $this->lastName;
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldInstanceType $emailAddress
	 */
	public function setEmailAddress(WebexXmlEventEnrollmentFieldInstanceType $emailAddress)
	{
		$this->emailAddress = $emailAddress;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldInstanceType $emailAddress
	 */
	public function getEmailAddress()
	{
		return $this->emailAddress;
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldInstanceType $phone
	 */
	public function setPhone(WebexXmlEventEnrollmentFieldInstanceType $phone)
	{
		$this->phone = $phone;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldInstanceType $phone
	 */
	public function getPhone()
	{
		return $this->phone;
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldInstanceType $company
	 */
	public function setCompany(WebexXmlEventEnrollmentFieldInstanceType $company)
	{
		$this->company = $company;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldInstanceType $company
	 */
	public function getCompany()
	{
		return $this->company;
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldInstanceType $title
	 */
	public function setTitle(WebexXmlEventEnrollmentFieldInstanceType $title)
	{
		$this->title = $title;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldInstanceType $title
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldInstanceType $numEmployees
	 */
	public function setNumEmployees(WebexXmlEventEnrollmentFieldInstanceType $numEmployees)
	{
		$this->numEmployees = $numEmployees;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldInstanceType $numEmployees
	 */
	public function getNumEmployees()
	{
		return $this->numEmployees;
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldInstanceType $futureInfo
	 */
	public function setFutureInfo(WebexXmlEventEnrollmentFieldInstanceType $futureInfo)
	{
		$this->futureInfo = $futureInfo;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldInstanceType $futureInfo
	 */
	public function getFutureInfo()
	{
		return $this->futureInfo;
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldInstanceType $address1
	 */
	public function setAddress1(WebexXmlEventEnrollmentFieldInstanceType $address1)
	{
		$this->address1 = $address1;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldInstanceType $address1
	 */
	public function getAddress1()
	{
		return $this->address1;
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldInstanceType $address2
	 */
	public function setAddress2(WebexXmlEventEnrollmentFieldInstanceType $address2)
	{
		$this->address2 = $address2;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldInstanceType $address2
	 */
	public function getAddress2()
	{
		return $this->address2;
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldInstanceType $city
	 */
	public function setCity(WebexXmlEventEnrollmentFieldInstanceType $city)
	{
		$this->city = $city;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldInstanceType $city
	 */
	public function getCity()
	{
		return $this->city;
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldInstanceType $state
	 */
	public function setState(WebexXmlEventEnrollmentFieldInstanceType $state)
	{
		$this->state = $state;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldInstanceType $state
	 */
	public function getState()
	{
		return $this->state;
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldInstanceType $postalCode
	 */
	public function setPostalCode(WebexXmlEventEnrollmentFieldInstanceType $postalCode)
	{
		$this->postalCode = $postalCode;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldInstanceType $postalCode
	 */
	public function getPostalCode()
	{
		return $this->postalCode;
	}
	
	/**
	 * @param WebexXmlEventEnrollmentFieldInstanceType $country
	 */
	public function setCountry(WebexXmlEventEnrollmentFieldInstanceType $country)
	{
		$this->country = $country;
	}
	
	/**
	 * @return WebexXmlEventEnrollmentFieldInstanceType $country
	 */
	public function getCountry()
	{
		return $this->country;
	}
	
}
		
