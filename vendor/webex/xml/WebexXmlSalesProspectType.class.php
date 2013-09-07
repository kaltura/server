<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSalesProspectType extends WebexXmlRequestType
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
	protected $firstName;
	
	/**
	 *
	 * @var string
	 */
	protected $lastName;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $email;
	
	/**
	 *
	 * @var WebexXmlComPhonesType
	 */
	protected $phones;
	
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
				return 'string';
	
			case 'lastName':
				return 'string';
	
			case 'email':
				return 'WebexXml';
	
			case 'phones':
				return 'WebexXmlComPhonesType';
	
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
			'email',
			'phones',
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
		return 'prospectType';
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
	 * @param WebexXml $email
	 */
	public function setEmail(WebexXml $email)
	{
		$this->email = $email;
	}
	
	/**
	 * @return WebexXml $email
	 */
	public function getEmail()
	{
		return $this->email;
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
	
}
		
