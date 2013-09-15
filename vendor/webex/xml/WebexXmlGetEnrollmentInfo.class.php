<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlAttEnrollSessionType.class.php');

class WebexXmlGetEnrollmentInfo extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlAttEnrollSessionType>
	 */
	protected $session;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'session':
				return 'WebexXmlArray<WebexXmlAttEnrollSessionType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $session
	 */
	public function getSession()
	{
		return $this->session;
	}
	
}

