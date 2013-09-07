<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteUrlType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $url;
	
	/**
	 *
	 * @var WebexXmlSiteWindowTargetType
	 */
	protected $target;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'url':
				return 'string';
	
			case 'target':
				return 'WebexXmlSiteWindowTargetType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'url',
			'target',
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
		return 'urlType';
	}
	
	/**
	 * @param string $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}
	
	/**
	 * @return string $url
	 */
	public function getUrl()
	{
		return $this->url;
	}
	
	/**
	 * @param WebexXmlSiteWindowTargetType $target
	 */
	public function setTarget(WebexXmlSiteWindowTargetType $target)
	{
		$this->target = $target;
	}
	
	/**
	 * @return WebexXmlSiteWindowTargetType $target
	 */
	public function getTarget()
	{
		return $this->target;
	}
	
}
		
