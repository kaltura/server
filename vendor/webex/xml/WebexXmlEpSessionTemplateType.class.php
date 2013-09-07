<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpSessionTemplateType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $name;
	
	/**
	 *
	 * @var WebexXmlEpTemplateTypeType
	 */
	protected $type;
	
	/**
	 *
	 * @var string
	 */
	protected $value;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'name':
				return 'string';
	
			case 'type':
				return 'WebexXmlEpTemplateTypeType';
	
			case 'value':
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
			'name',
			'type',
			'value',
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
		return 'sessionTemplateType';
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
	 * @param WebexXmlEpTemplateTypeType $type
	 */
	public function setType(WebexXmlEpTemplateTypeType $type)
	{
		$this->type = $type;
	}
	
	/**
	 * @return WebexXmlEpTemplateTypeType $type
	 */
	public function getType()
	{
		return $this->type;
	}
	
	/**
	 * @param string $value
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}
	
	/**
	 * @return string $value
	 */
	public function getValue()
	{
		return $this->value;
	}
	
}

