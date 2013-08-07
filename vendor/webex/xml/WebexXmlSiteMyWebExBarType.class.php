<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteMyWebExBarType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlSiteCustomLinkType
	 */
	protected $customLinks;
	
	/**
	 *
	 * @var WebexXmlSitePartnerLinkType
	 */
	protected $partnerLinks;
	
	/**
	 *
	 * @var boolean
	 */
	protected $partnerIntegration;
	
	/**
	 *
	 * @var WebexXmlSiteUrlType
	 */
	protected $support;
	
	/**
	 *
	 * @var WebexXmlSiteUrlType
	 */
	protected $training;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'customLinks':
				return 'WebexXmlSiteCustomLinkType';
	
			case 'partnerLinks':
				return 'WebexXmlSitePartnerLinkType';
	
			case 'partnerIntegration':
				return 'boolean';
	
			case 'support':
				return 'WebexXmlSiteUrlType';
	
			case 'training':
				return 'WebexXmlSiteUrlType';
	
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
			'partnerLinks',
			'partnerIntegration',
			'support',
			'training',
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
		return 'myWebExBarType';
	}
	
	/**
	 * @param WebexXmlSiteCustomLinkType $customLinks
	 */
	public function setCustomLinks(WebexXmlSiteCustomLinkType $customLinks)
	{
		$this->customLinks = $customLinks;
	}
	
	/**
	 * @return WebexXmlSiteCustomLinkType $customLinks
	 */
	public function getCustomLinks()
	{
		return $this->customLinks;
	}
	
	/**
	 * @param WebexXmlSitePartnerLinkType $partnerLinks
	 */
	public function setPartnerLinks(WebexXmlSitePartnerLinkType $partnerLinks)
	{
		$this->partnerLinks = $partnerLinks;
	}
	
	/**
	 * @return WebexXmlSitePartnerLinkType $partnerLinks
	 */
	public function getPartnerLinks()
	{
		return $this->partnerLinks;
	}
	
	/**
	 * @param boolean $partnerIntegration
	 */
	public function setPartnerIntegration($partnerIntegration)
	{
		$this->partnerIntegration = $partnerIntegration;
	}
	
	/**
	 * @return boolean $partnerIntegration
	 */
	public function getPartnerIntegration()
	{
		return $this->partnerIntegration;
	}
	
	/**
	 * @param WebexXmlSiteUrlType $support
	 */
	public function setSupport(WebexXmlSiteUrlType $support)
	{
		$this->support = $support;
	}
	
	/**
	 * @return WebexXmlSiteUrlType $support
	 */
	public function getSupport()
	{
		return $this->support;
	}
	
	/**
	 * @param WebexXmlSiteUrlType $training
	 */
	public function setTraining(WebexXmlSiteUrlType $training)
	{
		$this->training = $training;
	}
	
	/**
	 * @return WebexXmlSiteUrlType $training
	 */
	public function getTraining()
	{
		return $this->training;
	}
	
}
		
