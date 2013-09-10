<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteUcfType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $ucfConfiguration;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'ucfConfiguration':
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
			'ucfConfiguration',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'ucfConfiguration',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'ucfType';
	}
	
	/**
	 * @param string $ucfConfiguration
	 */
	public function setUcfConfiguration($ucfConfiguration)
	{
		$this->ucfConfiguration = $ucfConfiguration;
	}
	
	/**
	 * @return string $ucfConfiguration
	 */
	public function getUcfConfiguration()
	{
		return $this->ucfConfiguration;
	}
	
}
		
