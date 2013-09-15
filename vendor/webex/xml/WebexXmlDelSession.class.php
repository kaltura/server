<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlServICalendarURLType.class.php');

class WebexXmlDelSession extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlServICalendarURLType
	 */
	protected $iCalendarURL;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'iCalendarURL':
				return 'WebexXmlServICalendarURLType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlServICalendarURLType $iCalendarURL
	 */
	public function getICalendarURL()
	{
		return $this->iCalendarURL;
	}
	
}

