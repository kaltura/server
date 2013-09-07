<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSalesICalendarURL extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $sme;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'sme':
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
			'sme',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'sme',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'iCalendarURL';
	}
	
	/**
	 * @param string $sme
	 */
	public function setSme($sme)
	{
		$this->sme = $sme;
	}
	
	/**
	 * @return string $sme
	 */
	public function getSme()
	{
		return $this->sme;
	}
	
}
		
