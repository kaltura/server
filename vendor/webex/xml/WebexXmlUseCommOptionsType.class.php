<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseCommOptionsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $prodAnnounce;
	
	/**
	 *
	 * @var boolean
	 */
	protected $trainingInfo;
	
	/**
	 *
	 * @var boolean
	 */
	protected $electronicInfo;
	
	/**
	 *
	 * @var boolean
	 */
	protected $promos;
	
	/**
	 *
	 * @var boolean
	 */
	protected $press;
	
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
			case 'prodAnnounce':
				return 'boolean';
	
			case 'trainingInfo':
				return 'boolean';
	
			case 'electronicInfo':
				return 'boolean';
	
			case 'promos':
				return 'boolean';
	
			case 'press':
				return 'boolean';
	
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
			'prodAnnounce',
			'trainingInfo',
			'electronicInfo',
			'promos',
			'press',
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
		return 'commOptionsType';
	}
	
	/**
	 * @param boolean $prodAnnounce
	 */
	public function setProdAnnounce($prodAnnounce)
	{
		$this->prodAnnounce = $prodAnnounce;
	}
	
	/**
	 * @return boolean $prodAnnounce
	 */
	public function getProdAnnounce()
	{
		return $this->prodAnnounce;
	}
	
	/**
	 * @param boolean $trainingInfo
	 */
	public function setTrainingInfo($trainingInfo)
	{
		$this->trainingInfo = $trainingInfo;
	}
	
	/**
	 * @return boolean $trainingInfo
	 */
	public function getTrainingInfo()
	{
		return $this->trainingInfo;
	}
	
	/**
	 * @param boolean $electronicInfo
	 */
	public function setElectronicInfo($electronicInfo)
	{
		$this->electronicInfo = $electronicInfo;
	}
	
	/**
	 * @return boolean $electronicInfo
	 */
	public function getElectronicInfo()
	{
		return $this->electronicInfo;
	}
	
	/**
	 * @param boolean $promos
	 */
	public function setPromos($promos)
	{
		$this->promos = $promos;
	}
	
	/**
	 * @return boolean $promos
	 */
	public function getPromos()
	{
		return $this->promos;
	}
	
	/**
	 * @param boolean $press
	 */
	public function setPress($press)
	{
		$this->press = $press;
	}
	
	/**
	 * @return boolean $press
	 */
	public function getPress()
	{
		return $this->press;
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
		
