<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/long.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXml.class.php');

class WebexXmlCreateMeetingAttendee extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<long>
	 */
	protected $attendeeId;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXml>
	 */
	protected $register;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'attendeeId':
				return 'WebexXmlArray<long>';
	
			case 'register':
				return 'WebexXmlArray<WebexXml>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $attendeeId
	 */
	public function getAttendeeId()
	{
		return $this->attendeeId;
	}
	
	/**
	 * @return WebexXmlArray $register
	 */
	public function getRegister()
	{
		return $this->register;
	}
	
}

