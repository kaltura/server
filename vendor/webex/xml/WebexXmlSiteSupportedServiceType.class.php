<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteSupportedServiceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $enabled;
	
	/**
	 *
	 * @var string
	 */
	protected $pageVersion;
	
	/**
	 *
	 * @var string
	 */
	protected $clientVersion;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'enabled':
				return 'boolean';
	
			case 'pageVersion':
				return 'string';
	
			case 'clientVersion':
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
			'enabled',
			'pageVersion',
			'clientVersion',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'enabled',
			'pageVersion',
			'clientVersion',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'supportedServiceType';
	}
	
	/**
	 * @param boolean $enabled
	 */
	public function setEnabled($enabled)
	{
		$this->enabled = $enabled;
	}
	
	/**
	 * @return boolean $enabled
	 */
	public function getEnabled()
	{
		return $this->enabled;
	}
	
	/**
	 * @param string $pageVersion
	 */
	public function setPageVersion($pageVersion)
	{
		$this->pageVersion = $pageVersion;
	}
	
	/**
	 * @return string $pageVersion
	 */
	public function getPageVersion()
	{
		return $this->pageVersion;
	}
	
	/**
	 * @param string $clientVersion
	 */
	public function setClientVersion($clientVersion)
	{
		$this->clientVersion = $clientVersion;
	}
	
	/**
	 * @return string $clientVersion
	 */
	public function getClientVersion()
	{
		return $this->clientVersion;
	}
	
}
		
