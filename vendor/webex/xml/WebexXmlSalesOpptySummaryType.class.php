<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSalesOpptySummaryType extends WebexXmlRequestType
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
	protected $intOpptyID;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $extOpptyID;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'name':
				return 'WebexXml';
	
			case 'intOpptyID':
				return 'integer';
	
			case 'extOpptyID':
				return 'WebexXml';
	
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
			'intOpptyID',
			'extOpptyID',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'name',
			'intOpptyID',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'opptySummaryType';
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
	 * @param integer $intOpptyID
	 */
	public function setIntOpptyID($intOpptyID)
	{
		$this->intOpptyID = $intOpptyID;
	}
	
	/**
	 * @return integer $intOpptyID
	 */
	public function getIntOpptyID()
	{
		return $this->intOpptyID;
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
	
}
		
