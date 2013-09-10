<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpDistListWithContactInstanceType extends WebexXmlRequestType
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
	 * @var WebexXmlArray<integer>
	 */
	protected $contactID;
	
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
	
			case 'contactID':
				return 'WebexXmlArray<integer>';
	
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
			'contactID',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'distListID',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'distListWithContactInstanceType';
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
	 * @param WebexXmlArray<integer> $contactID
	 */
	public function setContactID($contactID)
	{
		if($contactID->getType() != 'integer')
			throw new WebexXmlException(get_class($this) . "::contactID must be of type integer");
		
		$this->contactID = $contactID;
	}
	
	/**
	 * @return WebexXmlArray $contactID
	 */
	public function getContactID()
	{
		return $this->contactID;
	}
	
}

