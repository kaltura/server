<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteInstallationOptionType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $autoUpdate;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'autoUpdate':
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
			'autoUpdate',
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
		return 'installationOptionType';
	}
	
	/**
	 * @param boolean $autoUpdate
	 */
	public function setAutoUpdate($autoUpdate)
	{
		$this->autoUpdate = $autoUpdate;
	}
	
	/**
	 * @return boolean $autoUpdate
	 */
	public function getAutoUpdate()
	{
		return $this->autoUpdate;
	}
	
}
		
