<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXml.class.php');

class WebexXmlListOpportunities extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<WebexXml>
	 */
	protected $opportunity;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'opportunity':
				return 'WebexXmlArray<WebexXml>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $opportunity
	 */
	public function getOpportunity()
	{
		return $this->opportunity;
	}
	
}
		
