<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlAttendeeFeedbackSessionType extends WebexXmlRequestType
{
	/**
	 *
	 * @var long
	 */
	protected $confID;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlAttFeedbackAttendeeType>
	 */
	protected $attendee;
	
	/**
	 *
	 * @var WebexXmlServMatchingRecordsType
	 */
	protected $matchingRecords;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'confID':
				return 'long';
	
			case 'attendee':
				return 'WebexXmlArray<WebexXmlAttFeedbackAttendeeType>';
	
			case 'matchingRecords':
				return 'WebexXmlServMatchingRecordsType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'confID',
			'attendee',
			'matchingRecords',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'confID',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'feedbackSessionType';
	}
	
	/**
	 * @param long $confID
	 */
	public function setConfID($confID)
	{
		$this->confID = $confID;
	}
	
	/**
	 * @return long $confID
	 */
	public function getConfID()
	{
		return $this->confID;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlAttFeedbackAttendeeType> $attendee
	 */
	public function setAttendee(WebexXmlArray $attendee)
	{
		if($attendee->getType() != 'WebexXmlAttFeedbackAttendeeType')
			throw new WebexXmlException(get_class($this) . "::attendee must be of type WebexXmlAttFeedbackAttendeeType");
		
		$this->attendee = $attendee;
	}
	
	/**
	 * @return WebexXmlArray $attendee
	 */
	public function getAttendee()
	{
		return $this->attendee;
	}
	
	/**
	 * @param WebexXmlServMatchingRecordsType $matchingRecords
	 */
	public function setMatchingRecords(WebexXmlServMatchingRecordsType $matchingRecords)
	{
		$this->matchingRecords = $matchingRecords;
	}
	
	/**
	 * @return WebexXmlServMatchingRecordsType $matchingRecords
	 */
	public function getMatchingRecords()
	{
		return $this->matchingRecords;
	}
	
}
		
