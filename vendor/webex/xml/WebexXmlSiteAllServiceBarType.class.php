<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteAllServiceBarType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $customLinks;
	
	/**
	 *
	 * @var WebexXmlSiteLinkType
	 */
	protected $support;
	
	/**
	 *
	 * @var WebexXmlSiteLinkType
	 */
	protected $training;
	
	/**
	 *
	 * @var WebexXmlSiteMenuType
	 */
	protected $supportMenu;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'customLinks':
				return 'WebexXml';
	
			case 'support':
				return 'WebexXmlSiteLinkType';
	
			case 'training':
				return 'WebexXmlSiteLinkType';
	
			case 'supportMenu':
				return 'WebexXmlSiteMenuType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'customLinks',
			'support',
			'training',
			'supportMenu',
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
		return 'allServiceBarType';
	}
	
	/**
	 * @param WebexXml $customLinks
	 */
	public function setCustomLinks(WebexXml $customLinks)
	{
		$this->customLinks = $customLinks;
	}
	
	/**
	 * @return WebexXml $customLinks
	 */
	public function getCustomLinks()
	{
		return $this->customLinks;
	}
	
	/**
	 * @param WebexXmlSiteLinkType $support
	 */
	public function setSupport(WebexXmlSiteLinkType $support)
	{
		$this->support = $support;
	}
	
	/**
	 * @return WebexXmlSiteLinkType $support
	 */
	public function getSupport()
	{
		return $this->support;
	}
	
	/**
	 * @param WebexXmlSiteLinkType $training
	 */
	public function setTraining(WebexXmlSiteLinkType $training)
	{
		$this->training = $training;
	}
	
	/**
	 * @return WebexXmlSiteLinkType $training
	 */
	public function getTraining()
	{
		return $this->training;
	}
	
	/**
	 * @param WebexXmlSiteMenuType $supportMenu
	 */
	public function setSupportMenu(WebexXmlSiteMenuType $supportMenu)
	{
		$this->supportMenu = $supportMenu;
	}
	
	/**
	 * @return WebexXmlSiteMenuType $supportMenu
	 */
	public function getSupportMenu()
	{
		return $this->supportMenu;
	}
	
}
		
