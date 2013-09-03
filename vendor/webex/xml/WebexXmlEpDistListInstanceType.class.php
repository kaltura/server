<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpDistListInstanceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var integer
	 */
	protected $distListID;
	
	/**
	 *
	 * @var string
	 */
	protected $name;
	
	/**
	 *
	 * @var string
	 */
	protected $desc;
	
	/**
	 *
	 * @var WebexXmlComAddressTypeType
	 */
	protected $addressType;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'distListID':
				return 'integer';
	
			case 'name':
				return 'string';
	
			case 'desc':
				return 'string';
	
			case 'addressType':
				return 'WebexXmlComAddressTypeType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'distListID',
			'name',
			'desc',
			'addressType',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'distListID',
			'name',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'distListInstanceType';
	}
	
	/**
	 * @param integer $distListID
	 */
	public function setDistListID($distListID)
	{
		$this->distListID = $distListID;
	}
	
	/**
	 * @return integer $distListID
	 */
	public function getDistListID()
	{
		return $this->distListID;
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
	 * @param string $desc
	 */
	public function setDesc($desc)
	{
		$this->desc = $desc;
	}
	
	/**
	 * @return string $desc
	 */
	public function getDesc()
	{
		return $this->desc;
	}
	
	/**
	 * @param WebexXmlComAddressTypeType $addressType
	 */
	public function setAddressType(WebexXmlComAddressTypeType $addressType)
	{
		$this->addressType = $addressType;
	}
	
	/**
	 * @return WebexXmlComAddressTypeType $addressType
	 */
	public function getAddressType()
	{
		return $this->addressType;
	}
	
}
		
