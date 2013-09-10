<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteCustomLinkType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlSiteLinkType>
	 */
	protected $customLink;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'customLink':
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
			'customLink',
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
		return 'customLinkType';
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlSiteLinkType> $customLink
	 */
	public function setCustomLink(WebexXmlArray $customLink)
	{
		if($customLink->getType() != 'WebexXmlSiteLinkType')
			throw new WebexXmlException(get_class($this) . "::customLink must be of type WebexXmlSiteLinkType");
		
		$this->customLink = $customLink;
	}
	
	/**
	 * @return WebexXmlArray $customLink
	 */
	public function getCustomLink()
	{
		return $this->customLink;
	}
	
}
		
