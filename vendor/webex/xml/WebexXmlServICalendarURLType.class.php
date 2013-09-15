<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlServICalendarURLType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $host;
	
	/**
	 *
	 * @var string
	 */
	protected $attendee;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'host':
				return 'string';
	
			case 'attendee':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'host',
			'attendee',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'host',
			'attendee',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'iCalendarURLType';
	}
	
	/**
	 * @param string $host
	 */
	public function setHost($host)
	{
		$this->host = $host;
	}
	
	/**
	 * @return string $host
	 */
	public function getHost()
	{
		return $this->host;
	}
	
	/**
	 * @param string $attendee
	 */
	public function setAttendee($attendee)
	{
		$this->attendee = $attendee;
	}
	
	/**
	 * @return string $attendee
	 */
	public function getAttendee()
	{
		return $this->attendee;
	}
	
}
		
