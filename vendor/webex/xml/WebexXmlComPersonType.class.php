<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlComPersonType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $name;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $firstName;
	
	/**
	 *
	 * @var WebexXml
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
	protected $webExId;
	
	/**
	 *
	 * @var WebexXmlComAddressType
	 */
	protected $address;
	
	/**
	 *
	 * @var WebexXmlComPhonesType
	 */
	protected $phones;
	
	/**
	 *
	 * @var string
	 */
	protected $email;
	
	/**
	 *
	 * @var string
	 */
	protected $notes;
	
	/**
	 *
	 * @var string
	 */
	protected $url;
	
	/**
	 *
	 * @var WebexXmlComPersonTypeType
	 */
	protected $type;
	
	/**
	 *
	 * @var boolean
	 */
	protected $sendReminder;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'name':
				return 'string';
	
			case 'firstName':
				return 'WebexXml';
	
			case 'lastName':
				return 'WebexXml';
	
			case 'title':
				return 'string';
	
			case 'company':
				return 'string';
	
			case 'webExId':
				return 'string';
	
			case 'address':
				return 'WebexXmlComAddressType';
	
			case 'phones':
				return 'WebexXmlComPhonesType';
	
			case 'email':
				return 'string';
	
			case 'notes':
				return 'string';
	
			case 'url':
				return 'string';
	
			case 'type':
				return 'WebexXmlComPersonTypeType';
	
			case 'sendReminder':
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
			'name',
			'firstName',
			'lastName',
			'title',
			'company',
			'webExId',
			'address',
			'phones',
			'email',
			'notes',
			'url',
			'type',
			'sendReminder',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'email',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'personType';
	}
	
	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}
	
	/**
	 * @return string $name
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * @param WebexXml $firstName
	 */
	public function setFirstName(WebexXml $firstName)
	{
		$this->firstName = $firstName;
	}
	
	/**
	 * @return WebexXml $firstName
	 */
	public function getFirstName()
	{
		return $this->firstName;
	}
	
	/**
	 * @param WebexXml $lastName
	 */
	public function setLastName(WebexXml $lastName)
	{
		$this->lastName = $lastName;
	}
	
	/**
	 * @return WebexXml $lastName
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
	 * @param string $webExId
	 */
	public function setWebExId($webExId)
	{
		$this->webExId = $webExId;
	}
	
	/**
	 * @return string $webExId
	 */
	public function getWebExId()
	{
		return $this->webExId;
	}
	
	/**
	 * @param WebexXmlComAddressType $address
	 */
	public function setAddress(WebexXmlComAddressType $address)
	{
		$this->address = $address;
	}
	
	/**
	 * @return WebexXmlComAddressType $address
	 */
	public function getAddress()
	{
		return $this->address;
	}
	
	/**
	 * @param WebexXmlComPhonesType $phones
	 */
	public function setPhones(WebexXmlComPhonesType $phones)
	{
		$this->phones = $phones;
	}
	
	/**
	 * @return WebexXmlComPhonesType $phones
	 */
	public function getPhones()
	{
		return $this->phones;
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
	 * @param string $notes
	 */
	public function setNotes($notes)
	{
		$this->notes = $notes;
	}
	
	/**
	 * @return string $notes
	 */
	public function getNotes()
	{
		return $this->notes;
	}
	
	/**
	 * @param string $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}
	
	/**
	 * @return string $url
	 */
	public function getUrl()
	{
		return $this->url;
	}
	
	/**
	 * @param WebexXmlComPersonTypeType $type
	 */
	public function setType(WebexXmlComPersonTypeType $type)
	{
		$this->type = $type;
	}
	
	/**
	 * @return WebexXmlComPersonTypeType $type
	 */
	public function getType()
	{
		return $this->type;
	}
	
	/**
	 * @param boolean $sendReminder
	 */
	public function setSendReminder($sendReminder)
	{
		$this->sendReminder = $sendReminder;
	}
	
	/**
	 * @return boolean $sendReminder
	 */
	public function getSendReminder()
	{
		return $this->sendReminder;
	}
	
}
		
