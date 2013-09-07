<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteTopBarType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlSiteNavigationBarType>
	 */
	protected $button;
	
	/**
	 *
	 * @var boolean
	 */
	protected $displayDisabledService;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'button':
				return 'WebexXmlArray<WebexXmlSiteNavigationBarType>';
	
			case 'displayDisabledService':
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
			'button',
			'displayDisabledService',
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
		return 'topBarType';
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlSiteNavigationBarType> $button
	 */
	public function setButton(WebexXmlArray $button)
	{
		if($button->getType() != 'WebexXmlSiteNavigationBarType')
			throw new WebexXmlException(get_class($this) . "::button must be of type WebexXmlSiteNavigationBarType");
		
		$this->button = $button;
	}
	
	/**
	 * @return WebexXmlArray $button
	 */
	public function getButton()
	{
		return $this->button;
	}
	
	/**
	 * @param boolean $displayDisabledService
	 */
	public function setDisplayDisabledService($displayDisabledService)
	{
		$this->displayDisabledService = $displayDisabledService;
	}
	
	/**
	 * @return boolean $displayDisabledService
	 */
	public function getDisplayDisabledService()
	{
		return $this->displayDisabledService;
	}
	
}
		
