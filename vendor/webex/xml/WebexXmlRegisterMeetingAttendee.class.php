<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXml.class.php');

class WebexXmlRegisterMeetingAttendee extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<WebexXml>
	 */
	protected $register;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'register':
				return 'WebexXmlArray<WebexXml>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $register
	 */
	public function getRegister()
	{
		return $this->register;
	}
	
}

