<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlSiteTimeZoneType.class.php');

class WebexXmlListTimeZone extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlSiteTimeZoneType>
	 */
	protected $timeZone;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'timeZone':
				return 'WebexXmlArray<WebexXmlSiteTimeZoneType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $timeZone
	 */
	public function getTimeZone()
	{
		return $this->timeZone;
	}
	
}
		
