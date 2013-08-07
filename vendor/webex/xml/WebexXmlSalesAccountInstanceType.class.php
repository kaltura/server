<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSalesAccountInstanceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var integer
	 */
	protected $intAccountID;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlSalesOpptySummaryType>
	 */
	protected $opportunity;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'intAccountID':
				return 'integer';
	
			case 'opportunity':
				return 'WebexXmlArray<WebexXmlSalesOpptySummaryType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'intAccountID',
			'opportunity',
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
		return 'accountInstanceType';
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
	 * @param WebexXmlArray<WebexXmlSalesOpptySummaryType> $opportunity
	 */
	public function setOpportunity(WebexXmlArray $opportunity)
	{
		if($opportunity->getType() != 'WebexXmlSalesOpptySummaryType')
			throw new WebexXmlException(get_class($this) . "::opportunity must be of type WebexXmlSalesOpptySummaryType");
		
		$this->opportunity = $opportunity;
	}
	
	/**
	 * @return WebexXmlArray $opportunity
	 */
	public function getOpportunity()
	{
		return $this->opportunity;
	}
	
}
		
