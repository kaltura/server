<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSalesAlternateHostType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlArray<string>
	 */
	protected $webExID;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'webExID':
				return 'WebexXmlArray<string>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'webExID',
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
		return 'alternateHostType';
	}
	
	/**
	 * @param WebexXmlArray<string> $webExID
	 */
	public function setWebExID($webExID)
	{
		if($webExID->getType() != 'string')
			throw new WebexXmlException(get_class($this) . "::webExID must be of type string");
		
		$this->webExID = $webExID;
	}
	
	/**
	 * @return WebexXmlArray $webExID
	 */
	public function getWebExID()
	{
		return $this->webExID;
	}
	
}
		
