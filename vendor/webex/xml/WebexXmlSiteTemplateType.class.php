<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteTemplateType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $useTemplate;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'useTemplate':
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
			'useTemplate',
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
		return 'templateType';
	}
	
	/**
	 * @param boolean $useTemplate
	 */
	public function setUseTemplate($useTemplate)
	{
		$this->useTemplate = $useTemplate;
	}
	
	/**
	 * @return boolean $useTemplate
	 */
	public function getUseTemplate()
	{
		return $this->useTemplate;
	}
	
}
		
