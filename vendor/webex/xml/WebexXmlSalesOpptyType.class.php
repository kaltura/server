<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSalesOpptyType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $name;
	
	/**
	 *
	 * @var integer
	 */
	protected $intAccountID;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $extOpptyID;
	
	/**
	 *
	 * @var integer
	 */
	protected $extSystemID;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'name':
				return 'WebexXml';
	
			case 'intAccountID':
				return 'integer';
	
			case 'extOpptyID':
				return 'WebexXml';
	
			case 'extSystemID':
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
			'name',
			'intAccountID',
			'extOpptyID',
			'extSystemID',
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
		return 'opptyType';
	}
	
	/**
	 * @param WebexXml $name
	 */
	public function setName(WebexXml $name)
	{
		$this->name = $name;
	}
	
	/**
	 * @return WebexXml $name
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * @param integer $intAccountID
	 */
	public function setIntAccountID($intAccountID)
	{
		$this->intAccountID = $intAccountID;
	}
	
	/**
	 * @return integer $intAccountID
	 */
	public function getIntAccountID()
	{
		return $this->intAccountID;
	}
	
	/**
	 * @param WebexXml $extOpptyID
	 */
	public function setExtOpptyID(WebexXml $extOpptyID)
	{
		$this->extOpptyID = $extOpptyID;
	}
	
	/**
	 * @return WebexXml $extOpptyID
	 */
	public function getExtOpptyID()
	{
		return $this->extOpptyID;
	}
	
	/**
	 * @param integer $extSystemID
	 */
	public function setExtSystemID($extSystemID)
	{
		$this->extSystemID = $extSystemID;
	}
	
	/**
	 * @return integer $extSystemID
	 */
	public function getExtSystemID()
	{
		return $this->extSystemID;
	}
	
}
		
