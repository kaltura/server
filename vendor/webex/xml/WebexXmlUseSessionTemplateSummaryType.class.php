<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseSessionTemplateSummaryType extends WebexXmlRequestType
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
	protected $value;
	
	/**
	 *
	 * @var WebexXmlComServiceTypeType
	 */
	protected $serviceType;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'name':
				return 'string';
	
			case 'value':
				return 'string';
	
			case 'serviceType':
				return 'WebexXmlComServiceTypeType';
	
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
			'value',
			'serviceType',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'name',
			'value',
			'serviceType',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'sessionTemplateSummaryType';
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
	
	/**
	 * @param WebexXmlComServiceTypeType $serviceType
	 */
	public function setServiceType(WebexXmlComServiceTypeType $serviceType)
	{
		$this->serviceType = $serviceType;
	}
	
	/**
	 * @return WebexXmlComServiceTypeType $serviceType
	 */
	public function getServiceType()
	{
		return $this->serviceType;
	}
	
}
		
