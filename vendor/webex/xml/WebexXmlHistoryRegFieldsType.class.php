<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlHistoryRegFieldsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $firstName;
	
	/**
	 *
	 * @var string
	 */
	protected $lastName;
	
	/**
	 *
	 * @var string
	 */
	protected $title;
	
	/**
	 *
	 * @var string
	 */
	protected $company;
	
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
	protected $postalCode;
	
	/**
	 *
	 * @var string
	 */
	protected $country;
	
	/**
	 *
	 * @var string
	 */
	protected $phone;
	
	/**
	 *
	 * @var string
	 */
	protected $fax;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlHistoryCustomFieldsType>
	 */
	protected $customFields;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'firstName':
				return 'string';
	
			case 'lastName':
				return 'string';
	
			case 'title':
				return 'string';
	
			case 'company':
				return 'string';
	
			case 'address1':
				return 'string';
	
			case 'address2':
				return 'string';
	
			case 'city':
				return 'string';
	
			case 'state':
				return 'string';
	
			case 'postalCode':
				return 'string';
	
			case 'country':
				return 'string';
	
			case 'phone':
				return 'string';
	
			case 'fax':
				return 'string';
	
			case 'customFields':
				return 'WebexXmlArray<WebexXmlHistoryCustomFieldsType>';
	
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
			'title',
			'company',
			'address1',
			'address2',
			'city',
			'state',
			'postalCode',
			'country',
			'phone',
			'fax',
			'customFields',
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
		return 'regFieldsType';
	}
	
	/**
	 * @param string $firstName
	 */
	public function setFirstName($firstName)
	{
		$this->firstName = $firstName;
	}
	
	/**
	 * @return string $firstName
	 */
	public function getFirstName()
	{
		return $this->firstName;
	}
	
	/**
	 * @param string $lastName
	 */
	public function setLastName($lastName)
	{
		$this->lastName = $lastName;
	}
	
	/**
	 * @return string $lastName
	 */
	public function getLastName()
	{
		return $this->lastName;
	}
	
	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	/**
	 * @return string $title
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	/**
	 * @param string $company
	 */
	public function setCompany($company)
	{
		$this->company = $company;
	}
	
	/**
	 * @return string $company
	 */
	public function getCompany()
	{
		return $this->company;
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
	 * @param string $postalCode
	 */
	public function setPostalCode($postalCode)
	{
		$this->postalCode = $postalCode;
	}
	
	/**
	 * @return string $postalCode
	 */
	public function getPostalCode()
	{
		return $this->postalCode;
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
	
	/**
	 * @param WebexXmlArray<WebexXmlHistoryCustomFieldsType> $customFields
	 */
	public function setCustomFields(WebexXmlArray $customFields)
	{
		if($customFields->getType() != 'WebexXmlHistoryCustomFieldsType')
			throw new WebexXmlException(get_class($this) . "::customFields must be of type WebexXmlHistoryCustomFieldsType");
		
		$this->customFields = $customFields;
	}
	
	/**
	 * @return WebexXmlArray $customFields
	 */
	public function getCustomFields()
	{
		return $this->customFields;
	}
	
}
		
