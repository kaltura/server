<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlAttendeeFeedbackAttendeeType extends WebexXmlRequestType
{
	/**
	 *
	 * @var long
	 */
	protected $attendeeID;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $feedbackFields;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'attendeeID':
				return 'long';
	
			case 'feedbackFields':
				return 'WebexXml';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'attendeeID',
			'feedbackFields',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'attendeeID',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'feedbackAttendeeType';
	}
	
	/**
	 * @param long $attendeeID
	 */
	public function setAttendeeID($attendeeID)
	{
		$this->attendeeID = $attendeeID;
	}
	
	/**
	 * @return long $attendeeID
	 */
	public function getAttendeeID()
	{
		return $this->attendeeID;
	}
	
	/**
	 * @param WebexXml $feedbackFields
	 */
	public function setFeedbackFields(WebexXml $feedbackFields)
	{
		$this->feedbackFields = $feedbackFields;
	}
	
	/**
	 * @return WebexXml $feedbackFields
	 */
	public function getFeedbackFields()
	{
		return $this->feedbackFields;
	}
	
}
		
