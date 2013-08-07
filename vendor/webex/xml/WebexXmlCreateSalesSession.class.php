<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlSalesICalendarURL.class.php');

class WebexXmlCreateSalesSession extends WebexXmlObject
{
	/**
	 *
	 * @var long
	 */
	protected $meetingKey;
	
	/**
	 *
	 * @var WebexXmlSalesICalendarURL
	 */
	protected $iCalendarURL;
	
	/**
	 *
	 * @var string
	 */
	protected $guestToken;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'meetingKey':
				return 'long';
	
			case 'iCalendarURL':
				return 'WebexXmlSalesICalendarURL';
	
			case 'guestToken':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return long $meetingKey
	 */
	public function getMeetingKey()
	{
		return $this->meetingKey;
	}
	
	/**
	 * @return WebexXmlSalesICalendarURL $iCalendarURL
	 */
	public function getICalendarURL()
	{
		return $this->iCalendarURL;
	}
	
	/**
	 * @return string $guestToken
	 */
	public function getGuestToken()
	{
		return $this->guestToken;
	}
	
}

