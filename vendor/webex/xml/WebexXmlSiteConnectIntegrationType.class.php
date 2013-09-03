<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteConnectIntegrationType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $connectServerURL;
	
	/**
	 *
	 * @var string
	 */
	protected $connectOrganization;
	
	/**
	 *
	 * @var string
	 */
	protected $connectNameSpaceID;
	
	/**
	 *
	 * @var boolean
	 */
	protected $integratedWebEx11;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'connectServerURL':
				return 'string';
	
			case 'connectOrganization':
				return 'string';
	
			case 'connectNameSpaceID':
				return 'string';
	
			case 'integratedWebEx11':
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
			'connectServerURL',
			'connectOrganization',
			'connectNameSpaceID',
			'integratedWebEx11',
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
		return 'connectIntegrationType';
	}
	
	/**
	 * @param string $connectServerURL
	 */
	public function setConnectServerURL($connectServerURL)
	{
		$this->connectServerURL = $connectServerURL;
	}
	
	/**
	 * @return string $connectServerURL
	 */
	public function getConnectServerURL()
	{
		return $this->connectServerURL;
	}
	
	/**
	 * @param string $connectOrganization
	 */
	public function setConnectOrganization($connectOrganization)
	{
		$this->connectOrganization = $connectOrganization;
	}
	
	/**
	 * @return string $connectOrganization
	 */
	public function getConnectOrganization()
	{
		return $this->connectOrganization;
	}
	
	/**
	 * @param string $connectNameSpaceID
	 */
	public function setConnectNameSpaceID($connectNameSpaceID)
	{
		$this->connectNameSpaceID = $connectNameSpaceID;
	}
	
	/**
	 * @return string $connectNameSpaceID
	 */
	public function getConnectNameSpaceID()
	{
		return $this->connectNameSpaceID;
	}
	
	/**
	 * @param boolean $integratedWebEx11
	 */
	public function setIntegratedWebEx11($integratedWebEx11)
	{
		$this->integratedWebEx11 = $integratedWebEx11;
	}
	
	/**
	 * @return boolean $integratedWebEx11
	 */
	public function getIntegratedWebEx11()
	{
		return $this->integratedWebEx11;
	}
	
}
		
