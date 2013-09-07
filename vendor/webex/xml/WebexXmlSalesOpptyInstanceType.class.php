<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSalesOpptyInstanceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var integer
	 */
	protected $intOpptyID;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'intOpptyID':
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
			'intOpptyID',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'intOpptyID',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'opptyInstanceType';
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
	
}

