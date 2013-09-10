<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpOneClickMetaData extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlComServiceTypeType
	 */
	protected $serviceType;
	
	/**
	 *
	 * @var integer
	 */
	protected $sessionType;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'serviceType':
				return 'WebexXmlComServiceTypeType';
	
			case 'sessionType':
				return 'integer';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'serviceType',
			'sessionType',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'serviceType',
			'sessionType',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'oneClickMetaData';
	}
	
	/**
	 * @param WebexXmlComServiceTypeType $serviceType
	 */
	public function setServiceType(WebexXmlComServiceTypeType $serviceType)
	{
		$this->serviceType = $serviceType;
	}
	
	/**
	 * @return WebexXmlComServiceTypeType $serviceType
	 */
	public function getServiceType()
	{
		return $this->serviceType;
	}
	
	/**
	 * @param integer $sessionType
	 */
	public function setSessionType($sessionType)
	{
		$this->sessionType = $sessionType;
	}
	
	/**
	 * @return integer $sessionType
	 */
	public function getSessionType()
	{
		return $this->sessionType;
	}
	
}
		
