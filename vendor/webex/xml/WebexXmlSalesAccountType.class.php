<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSalesAccountType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $name;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $extAccountID;
	
	/**
	 *
	 * @var integer
	 */
	protected $extSystemID;
	
	/**
	 *
	 * @var integer
	 */
	protected $parentIntID;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'name':
				return 'WebexXml';
	
			case 'extAccountID':
				return 'WebexXml';
	
			case 'extSystemID':
				return 'integer';
	
			case 'parentIntID':
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
			'extAccountID',
			'extSystemID',
			'parentIntID',
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
		return 'accountType';
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
	 * @param WebexXml $extAccountID
	 */
	public function setExtAccountID(WebexXml $extAccountID)
	{
		$this->extAccountID = $extAccountID;
	}
	
	/**
	 * @return WebexXml $extAccountID
	 */
	public function getExtAccountID()
	{
		return $this->extAccountID;
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
	
	/**
	 * @param integer $parentIntID
	 */
	public function setParentIntID($parentIntID)
	{
		$this->parentIntID = $parentIntID;
	}
	
	/**
	 * @return integer $parentIntID
	 */
	public function getParentIntID()
	{
		return $this->parentIntID;
	}
	
}
		
