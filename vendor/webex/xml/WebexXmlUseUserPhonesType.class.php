<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseUserPhonesType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $phone2;
	
	/**
	 *
	 * @var string
	 */
	protected $mobilePhone2;
	
	/**
	 *
	 * @var string
	 */
	protected $pager;
	
	/**
	 *
	 * @var string
	 */
	protected $PIN;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'phone2':
				return 'string';
	
			case 'mobilePhone2':
				return 'string';
	
			case 'pager':
				return 'string';
	
			case 'PIN':
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
			'phone2',
			'mobilePhone2',
			'pager',
			'PIN',
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
		return 'userPhonesType';
	}
	
	/**
	 * @param string $phone2
	 */
	public function setPhone2($phone2)
	{
		$this->phone2 = $phone2;
	}
	
	/**
	 * @return string $phone2
	 */
	public function getPhone2()
	{
		return $this->phone2;
	}
	
	/**
	 * @param string $mobilePhone2
	 */
	public function setMobilePhone2($mobilePhone2)
	{
		$this->mobilePhone2 = $mobilePhone2;
	}
	
	/**
	 * @return string $mobilePhone2
	 */
	public function getMobilePhone2()
	{
		return $this->mobilePhone2;
	}
	
	/**
	 * @param string $pager
	 */
	public function setPager($pager)
	{
		$this->pager = $pager;
	}
	
	/**
	 * @return string $pager
	 */
	public function getPager()
	{
		return $this->pager;
	}
	
	/**
	 * @param string $PIN
	 */
	public function setPIN($PIN)
	{
		$this->PIN = $PIN;
	}
	
	/**
	 * @return string $PIN
	 */
	public function getPIN()
	{
		return $this->PIN;
	}
	
}
		
