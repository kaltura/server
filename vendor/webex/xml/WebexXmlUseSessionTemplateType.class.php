<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseSessionTemplateType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $name;
	
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
			'serviceType',
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
		
