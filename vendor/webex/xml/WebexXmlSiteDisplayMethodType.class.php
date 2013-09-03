<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteDisplayMethodType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $email;
	
	/**
	 *
	 * @var boolean
	 */
	protected $fax;
	
	/**
	 *
	 * @var boolean
	 */
	protected $phone;
	
	/**
	 *
	 * @var boolean
	 */
	protected $mail;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'email':
				return 'boolean';
	
			case 'fax':
				return 'boolean';
	
			case 'phone':
				return 'boolean';
	
			case 'mail':
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
			'email',
			'fax',
			'phone',
			'mail',
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
		return 'displayMethodType';
	}
	
	/**
	 * @param boolean $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}
	
	/**
	 * @return boolean $email
	 */
	public function getEmail()
	{
		return $this->email;
	}
	
	/**
	 * @param boolean $fax
	 */
	public function setFax($fax)
	{
		$this->fax = $fax;
	}
	
	/**
	 * @return boolean $fax
	 */
	public function getFax()
	{
		return $this->fax;
	}
	
	/**
	 * @param boolean $phone
	 */
	public function setPhone($phone)
	{
		$this->phone = $phone;
	}
	
	/**
	 * @return boolean $phone
	 */
	public function getPhone()
	{
		return $this->phone;
	}
	
	/**
	 * @param boolean $mail
	 */
	public function setMail($mail)
	{
		$this->mail = $mail;
	}
	
	/**
	 * @return boolean $mail
	 */
	public function getMail()
	{
		return $this->mail;
	}
	
}
		
