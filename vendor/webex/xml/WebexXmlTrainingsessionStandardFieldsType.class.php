<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTrainingsessionStandardFieldsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlTrainEnrollmentFieldType
	 */
	protected $phone;
	
	/**
	 *
	 * @var WebexXmlTrainEnrollmentFieldType
	 */
	protected $title;
	
	/**
	 *
	 * @var WebexXmlTrainEnrollmentFieldType
	 */
	protected $company;
	
	/**
	 *
	 * @var WebexXmlTrainEnrollmentFieldType
	 */
	protected $address1;
	
	/**
	 *
	 * @var WebexXmlTrainEnrollmentFieldType
	 */
	protected $address2;
	
	/**
	 *
	 * @var WebexXmlTrainEnrollmentFieldType
	 */
	protected $city;
	
	/**
	 *
	 * @var WebexXmlTrainEnrollmentFieldType
	 */
	protected $state;
	
	/**
	 *
	 * @var WebexXmlTrainEnrollmentFieldType
	 */
	protected $postalCode;
	
	/**
	 *
	 * @var WebexXmlTrainEnrollmentFieldType
	 */
	protected $country;
	
	/**
	 *
	 * @var WebexXmlTrainEnrollmentFieldType
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
				return 'WebexXmlTrainEnrollmentFieldType';
	
			case 'title':
				return 'WebexXmlTrainEnrollmentFieldType';
	
			case 'company':
				return 'WebexXmlTrainEnrollmentFieldType';
	
			case 'address1':
				return 'WebexXmlTrainEnrollmentFieldType';
	
			case 'address2':
				return 'WebexXmlTrainEnrollmentFieldType';
	
			case 'city':
				return 'WebexXmlTrainEnrollmentFieldType';
	
			case 'state':
				return 'WebexXmlTrainEnrollmentFieldType';
	
			case 'postalCode':
				return 'WebexXmlTrainEnrollmentFieldType';
	
			case 'country':
				return 'WebexXmlTrainEnrollmentFieldType';
	
			case 'fax':
				return 'WebexXmlTrainEnrollmentFieldType';
	
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
	 * @param WebexXmlTrainEnrollmentFieldType $phone
	 */
	public function setPhone(WebexXmlTrainEnrollmentFieldType $phone)
	{
		$this->phone = $phone;
	}
	
	/**
	 * @return WebexXmlTrainEnrollmentFieldType $phone
	 */
	public function getPhone()
	{
		return $this->phone;
	}
	
	/**
	 * @param WebexXmlTrainEnrollmentFieldType $title
	 */
	public function setTitle(WebexXmlTrainEnrollmentFieldType $title)
	{
		$this->title = $title;
	}
	
	/**
	 * @return WebexXmlTrainEnrollmentFieldType $title
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	/**
	 * @param WebexXmlTrainEnrollmentFieldType $company
	 */
	public function setCompany(WebexXmlTrainEnrollmentFieldType $company)
	{
		$this->company = $company;
	}
	
	/**
	 * @return WebexXmlTrainEnrollmentFieldType $company
	 */
	public function getCompany()
	{
		return $this->company;
	}
	
	/**
	 * @param WebexXmlTrainEnrollmentFieldType $address1
	 */
	public function setAddress1(WebexXmlTrainEnrollmentFieldType $address1)
	{
		$this->address1 = $address1;
	}
	
	/**
	 * @return WebexXmlTrainEnrollmentFieldType $address1
	 */
	public function getAddress1()
	{
		return $this->address1;
	}
	
	/**
	 * @param WebexXmlTrainEnrollmentFieldType $address2
	 */
	public function setAddress2(WebexXmlTrainEnrollmentFieldType $address2)
	{
		$this->address2 = $address2;
	}
	
	/**
	 * @return WebexXmlTrainEnrollmentFieldType $address2
	 */
	public function getAddress2()
	{
		return $this->address2;
	}
	
	/**
	 * @param WebexXmlTrainEnrollmentFieldType $city
	 */
	public function setCity(WebexXmlTrainEnrollmentFieldType $city)
	{
		$this->city = $city;
	}
	
	/**
	 * @return WebexXmlTrainEnrollmentFieldType $city
	 */
	public function getCity()
	{
		return $this->city;
	}
	
	/**
	 * @param WebexXmlTrainEnrollmentFieldType $state
	 */
	public function setState(WebexXmlTrainEnrollmentFieldType $state)
	{
		$this->state = $state;
	}
	
	/**
	 * @return WebexXmlTrainEnrollmentFieldType $state
	 */
	public function getState()
	{
		return $this->state;
	}
	
	/**
	 * @param WebexXmlTrainEnrollmentFieldType $postalCode
	 */
	public function setPostalCode(WebexXmlTrainEnrollmentFieldType $postalCode)
	{
		$this->postalCode = $postalCode;
	}
	
	/**
	 * @return WebexXmlTrainEnrollmentFieldType $postalCode
	 */
	public function getPostalCode()
	{
		return $this->postalCode;
	}
	
	/**
	 * @param WebexXmlTrainEnrollmentFieldType $country
	 */
	public function setCountry(WebexXmlTrainEnrollmentFieldType $country)
	{
		$this->country = $country;
	}
	
	/**
	 * @return WebexXmlTrainEnrollmentFieldType $country
	 */
	public function getCountry()
	{
		return $this->country;
	}
	
	/**
	 * @param WebexXmlTrainEnrollmentFieldType $fax
	 */
	public function setFax(WebexXmlTrainEnrollmentFieldType $fax)
	{
		$this->fax = $fax;
	}
	
	/**
	 * @return WebexXmlTrainEnrollmentFieldType $fax
	 */
	public function getFax()
	{
		return $this->fax;
	}
	
}
		
