<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSitePartnerLinkType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlSiteLinkType>
	 */
	protected $partnerLink;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'partnerLink':
				return 'WebexXmlArray<WebexXmlSiteLinkType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'partnerLink',
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
		return 'partnerLinkType';
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlSiteLinkType> $partnerLink
	 */
	public function setPartnerLink(WebexXmlArray $partnerLink)
	{
		if($partnerLink->getType() != 'WebexXmlSiteLinkType')
			throw new WebexXmlException(get_class($this) . "::partnerLink must be of type WebexXmlSiteLinkType");
		
		$this->partnerLink = $partnerLink;
	}
	
	/**
	 * @return WebexXmlArray $partnerLink
	 */
	public function getPartnerLink()
	{
		return $this->partnerLink;
	}
	
}
		
