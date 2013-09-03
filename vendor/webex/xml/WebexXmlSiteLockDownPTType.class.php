<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteLockDownPTType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $lockDown;
	
	/**
	 *
	 * @var string
	 */
	protected $version;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'lockDown':
				return 'boolean';
	
			case 'version':
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
			'lockDown',
			'version',
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
		return 'lockDownPTType';
	}
	
	/**
	 * @param boolean $lockDown
	 */
	public function setLockDown($lockDown)
	{
		$this->lockDown = $lockDown;
	}
	
	/**
	 * @return boolean $lockDown
	 */
	public function getLockDown()
	{
		return $this->lockDown;
	}
	
	/**
	 * @param string $version
	 */
	public function setVersion($version)
	{
		$this->version = $version;
	}
	
	/**
	 * @return string $version
	 */
	public function getVersion()
	{
		return $this->version;
	}
	
}
		
