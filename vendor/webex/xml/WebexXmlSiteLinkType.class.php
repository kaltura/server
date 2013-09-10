<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteLinkType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $name;
	
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
	
	/**
	 *
	 * @var string
	 */
	protected $iconURL;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'name':
				return 'string';
	
			case 'url':
				return 'string';
	
			case 'target':
				return 'WebexXmlSiteWindowTargetType';
	
			case 'iconURL':
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
			'name',
			'url',
			'target',
			'iconURL',
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
		return 'linkType';
	}
	
	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}
	
	/**
	 * @return string $name
	 */
	public function getName()
	{
		return $this->name;
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
	
	/**
	 * @param string $iconURL
	 */
	public function setIconURL($iconURL)
	{
		$this->iconURL = $iconURL;
	}
	
	/**
	 * @return string $iconURL
	 */
	public function getIconURL()
	{
		return $this->iconURL;
	}
	
}
		
