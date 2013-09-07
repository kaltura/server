<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventAttendeesType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlArray<WebexXml>
	 */
	protected $attendee;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'attendee':
				return 'WebexXmlArray<WebexXml>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'attendee',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'attendeesType';
	}
	
	/**
	 * @param WebexXmlArray<WebexXml> $attendee
	 */
	public function setAttendee(WebexXmlArray $attendee)
	{
		if($attendee->getType() != 'WebexXml')
			throw new WebexXmlException(get_class($this) . "::attendee must be of type WebexXml");
		
		$this->attendee = $attendee;
	}
	
	/**
	 * @return WebexXmlArray $attendee
	 */
	public function getAttendee()
	{
		return $this->attendee;
	}
	
}
		
