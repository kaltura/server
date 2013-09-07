<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');

class WebexXmlGethosturlMeeting extends WebexXmlObject
{
	/**
	 *
	 * @var string
	 */
	protected $hostMeetingURL;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'hostMeetingURL':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return string $hostMeetingURL
	 */
	public function getHostMeetingURL()
	{
		return $this->hostMeetingURL;
	}
	
}

