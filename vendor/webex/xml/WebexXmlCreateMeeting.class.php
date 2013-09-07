<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlServICalendarURLType.class.php');

class WebexXmlCreateMeeting extends WebexXmlObject
{
	/**
	 *
	 * @var long
	 */
	protected $meetingkey;
	
	/**
	 *
	 * @var WebexXmlServICalendarURLType
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
			case 'meetingkey':
				return 'long';
	
			case 'iCalendarURL':
				return 'WebexXmlServICalendarURLType';
	
			case 'guestToken':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return long $meetingkey
	 */
	public function getMeetingkey()
	{
		return $this->meetingkey;
	}
	
	/**
	 * @return WebexXmlServICalendarURLType $iCalendarURL
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

