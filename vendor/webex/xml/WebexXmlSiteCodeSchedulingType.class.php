<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteCodeSchedulingType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $service;
	
	/**
	 *
	 * @var WebexXmlSiteCodeDisplayType
	 */
	protected $scheduling;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'service':
				return 'string';
	
			case 'scheduling':
				return 'WebexXmlSiteCodeDisplayType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'service',
			'scheduling',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'service',
			'scheduling',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'codeSchedulingType';
	}
	
	/**
	 * @param string $service
	 */
	public function setService($service)
	{
		$this->service = $service;
	}
	
	/**
	 * @return string $service
	 */
	public function getService()
	{
		return $this->service;
	}
	
	/**
	 * @param WebexXmlSiteCodeDisplayType $scheduling
	 */
	public function setScheduling(WebexXmlSiteCodeDisplayType $scheduling)
	{
		$this->scheduling = $scheduling;
	}
	
	/**
	 * @return WebexXmlSiteCodeDisplayType $scheduling
	 */
	public function getScheduling()
	{
		return $this->scheduling;
	}
	
}
		
