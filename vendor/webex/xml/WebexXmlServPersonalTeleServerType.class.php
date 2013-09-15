<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlServPersonalTeleServerType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $enableServer;
	
	/**
	 *
	 * @var string
	 */
	protected $tollLabel;
	
	/**
	 *
	 * @var string
	 */
	protected $tollFreeLabel;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'enableServer':
				return 'boolean';
	
			case 'tollLabel':
				return 'string';
	
			case 'tollFreeLabel':
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
			'enableServer',
			'tollLabel',
			'tollFreeLabel',
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
		return 'personalTeleServerType';
	}
	
	/**
	 * @param boolean $enableServer
	 */
	public function setEnableServer($enableServer)
	{
		$this->enableServer = $enableServer;
	}
	
	/**
	 * @return boolean $enableServer
	 */
	public function getEnableServer()
	{
		return $this->enableServer;
	}
	
	/**
	 * @param string $tollLabel
	 */
	public function setTollLabel($tollLabel)
	{
		$this->tollLabel = $tollLabel;
	}
	
	/**
	 * @return string $tollLabel
	 */
	public function getTollLabel()
	{
		return $this->tollLabel;
	}
	
	/**
	 * @param string $tollFreeLabel
	 */
	public function setTollFreeLabel($tollFreeLabel)
	{
		$this->tollFreeLabel = $tollFreeLabel;
	}
	
	/**
	 * @return string $tollFreeLabel
	 */
	public function getTollFreeLabel()
	{
		return $this->tollFreeLabel;
	}
	
}
		
