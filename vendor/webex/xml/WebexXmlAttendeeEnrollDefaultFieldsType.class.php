<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlAttendeeEnrollDefaultFieldsType extends WebexXmlRequestType
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
	protected $email;
	
	/**
	 *
	 * @var string
	 */
	protected $company;
	
	/**
	 *
	 * @var string
	 */
	protected $phoneNum;
	
	/**
	 *
	 * @var string
	 */
	protected $title;
	
	/**
	 *
	 * @var string
	 */
	protected $numEmployees;
	
	/**
	 *
	 * @var boolean
	 */
	protected $receiveInfo;
	
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
	protected $zipcode;
	
	/**
	 *
	 * @var string
	 */
	protected $country;
	
	/**
	 *
	 * @var string
	 */
	protected $leadSourceID;
	
	/**
	 *
	 * @var string
	 */
	protected $leadScore;
	
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
	
			case 'email':
				return 'string';
	
			case 'company':
				return 'string';
	
			case 'phoneNum':
				return 'string';
	
			case 'title':
				return 'string';
	
			case 'numEmployees':
				return 'string';
	
			case 'receiveInfo':
				return 'boolean';
	
			case 'address1':
				return 'string';
	
			case 'address2':
				return 'string';
	
			case 'city':
				return 'string';
	
			case 'state':
				return 'string';
	
			case 'zipcode':
				return 'string';
	
			case 'country':
				return 'string';
	
			case 'leadSourceID':
				return 'string';
	
			case 'leadScore':
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
			'firstName',
			'lastName',
			'email',
			'company',
			'phoneNum',
			'title',
			'numEmployees',
			'receiveInfo',
			'address1',
			'address2',
			'city',
			'state',
			'zipcode',
			'country',
			'leadSourceID',
			'leadScore',
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
		return 'enrollDefaultFieldsType';
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
	 * @param string $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}
	
	/**
	 * @return string $email
	 */
	public function getEmail()
	{
		return $this->email;
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
	 * @param string $phoneNum
	 */
	public function setPhoneNum($phoneNum)
	{
		$this->phoneNum = $phoneNum;
	}
	
	/**
	 * @return string $phoneNum
	 */
	public function getPhoneNum()
	{
		return $this->phoneNum;
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
	 * @param string $numEmployees
	 */
	public function setNumEmployees($numEmployees)
	{
		$this->numEmployees = $numEmployees;
	}
	
	/**
	 * @return string $numEmployees
	 */
	public function getNumEmployees()
	{
		return $this->numEmployees;
	}
	
	/**
	 * @param boolean $receiveInfo
	 */
	public function setReceiveInfo($receiveInfo)
	{
		$this->receiveInfo = $receiveInfo;
	}
	
	/**
	 * @return boolean $receiveInfo
	 */
	public function getReceiveInfo()
	{
		return $this->receiveInfo;
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
	 * @param string $zipcode
	 */
	public function setZipcode($zipcode)
	{
		$this->zipcode = $zipcode;
	}
	
	/**
	 * @return string $zipcode
	 */
	public function getZipcode()
	{
		return $this->zipcode;
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
	 * @param string $leadSourceID
	 */
	public function setLeadSourceID($leadSourceID)
	{
		$this->leadSourceID = $leadSourceID;
	}
	
	/**
	 * @return string $leadSourceID
	 */
	public function getLeadSourceID()
	{
		return $this->leadSourceID;
	}
	
	/**
	 * @param string $leadScore
	 */
	public function setLeadScore($leadScore)
	{
		$this->leadScore = $leadScore;
	}
	
	/**
	 * @return string $leadScore
	 */
	public function getLeadScore()
	{
		return $this->leadScore;
	}
	
}
		
