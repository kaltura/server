<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTeleconferenceonlyMetaDataType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $confName;
	
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
			case 'confName':
				return 'WebexXml';
	
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
			'confName',
			'sessionType',
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
		return 'metaDataType';
	}
	
	/**
	 * @param WebexXml $confName
	 */
	public function setConfName(WebexXml $confName)
	{
		$this->confName = $confName;
	}
	
	/**
	 * @return WebexXml $confName
	 */
	public function getConfName()
	{
		return $this->confName;
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
		
