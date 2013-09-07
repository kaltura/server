<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteCustCommunicationsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlSiteDisplayTypeType
	 */
	protected $displayType;
	
	/**
	 *
	 * @var WebexXmlSiteDisplayMethodType
	 */
	protected $displayMethod;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'displayType':
				return 'WebexXmlSiteDisplayTypeType';
	
			case 'displayMethod':
				return 'WebexXmlSiteDisplayMethodType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'displayType',
			'displayMethod',
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
		return 'custCommunicationsType';
	}
	
	/**
	 * @param WebexXmlSiteDisplayTypeType $displayType
	 */
	public function setDisplayType(WebexXmlSiteDisplayTypeType $displayType)
	{
		$this->displayType = $displayType;
	}
	
	/**
	 * @return WebexXmlSiteDisplayTypeType $displayType
	 */
	public function getDisplayType()
	{
		return $this->displayType;
	}
	
	/**
	 * @param WebexXmlSiteDisplayMethodType $displayMethod
	 */
	public function setDisplayMethod(WebexXmlSiteDisplayMethodType $displayMethod)
	{
		$this->displayMethod = $displayMethod;
	}
	
	/**
	 * @return WebexXmlSiteDisplayMethodType $displayMethod
	 */
	public function getDisplayMethod()
	{
		return $this->displayMethod;
	}
	
}
		
