<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpDistListWithContactType extends WebexXmlRequestType
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
			'name',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'distListWithContactType';
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
		
