<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlSalesICalendarURL.class.php');

class WebexXmlDelSalesSession extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlSalesICalendarURL
	 */
	protected $iCalendarURL;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'iCalendarURL':
				return 'WebexXmlSalesICalendarURL';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlSalesICalendarURL $iCalendarURL
	 */
	public function getICalendarURL()
	{
		return $this->iCalendarURL;
	}
	
}
		
