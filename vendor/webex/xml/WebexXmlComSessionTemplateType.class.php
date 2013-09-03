<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlComSessionTemplateType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $use;
	
	/**
	 *
	 * @var boolean
	 */
	protected $default;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'use':
				return 'string';
	
			case 'default':
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
			'use',
			'default',
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
	 * @param string $use
	 */
	public function setUse($use)
	{
		$this->use = $use;
	}
	
	/**
	 * @return string $use
	 */
	public function getUse()
	{
		return $this->use;
	}
	
	/**
	 * @param boolean $default
	 */
	public function setDefault($default)
	{
		$this->default = $default;
	}
	
	/**
	 * @return boolean $default
	 */
	public function getDefault()
	{
		return $this->default;
	}
	
}
		
