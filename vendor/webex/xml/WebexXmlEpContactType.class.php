<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpContactType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $name;
	
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
	 * @var WebexXmlComJoinStatusType
	 */
	protected $joinStatus;
	
	/**
	 *
	 * @var string
	 */
	protected $language;
	
	/**
	 *
	 * @var string
	 */
	protected $locale;
	
	/**
	 *
	 * @var long
	 */
	protected $timeZoneID;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'name':
				return 'string';
	
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
	
			case 'joinStatus':
				return 'WebexXmlComJoinStatusType';
	
			case 'language':
				return 'string';
	
			case 'locale':
				return 'string';
	
			case 'timeZoneID':
				return 'long';
	
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
			'title',
			'company',
			'webExId',
			'address',
			'phones',
			'email',
			'notes',
			'url',
			'type',
			'joinStatus',
			'language',
			'locale',
			'timeZoneID',
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
		return 'contactType';
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
	 * @param WebexXmlComJoinStatusType $joinStatus
	 */
	public function setJoinStatus(WebexXmlComJoinStatusType $joinStatus)
	{
		$this->joinStatus = $joinStatus;
	}
	
	/**
	 * @return WebexXmlComJoinStatusType $joinStatus
	 */
	public function getJoinStatus()
	{
		return $this->joinStatus;
	}
	
	/**
	 * @param string $language
	 */
	public function setLanguage($language)
	{
		$this->language = $language;
	}
	
	/**
	 * @return string $language
	 */
	public function getLanguage()
	{
		return $this->language;
	}
	
	/**
	 * @param string $locale
	 */
	public function setLocale($locale)
	{
		$this->locale = $locale;
	}
	
	/**
	 * @return string $locale
	 */
	public function getLocale()
	{
		return $this->locale;
	}
	
	/**
	 * @param long $timeZoneID
	 */
	public function setTimeZoneID($timeZoneID)
	{
		$this->timeZoneID = $timeZoneID;
	}
	
	/**
	 * @return long $timeZoneID
	 */
	public function getTimeZoneID()
	{
		return $this->timeZoneID;
	}
	
}
		
