<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');

class WebexXmlFailedToDeleteRecordings extends WebexXmlRequestType
{
	/**
	 *
	 * @var int
	 */
	protected $recordingID;
	
	/**
	 * string
	 */
	protected $reasonForFailure;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'recordingID':
				return 'int';
			case 'reasonForFailure':
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
			'recordingId',
			'reasonForFailure',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'recordingId',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'failedToDeleteRecordings';
	}
	
	/**
	 * @param WebexXmlArray<integer> $recordingId
	 */
	public function setRecordingId($recordingId)
	{
		$this->recordingID = $recordingId;
	}
	
	/**
	 * @return WebexXmlArray<integer> $recordingId
	 */
	public function getRecordingId()
	{
		return $this->recordingID;
	}
	
	/**
	 * @param string $reasonForFailure
	 */
	public function setReasonForFailure($reasonForFailure)
	{
		$this->reasonForFailure = $reasonForFailure;
	}
	
	/**
	 * @return string $reasonForFailure
	 */
	public function getReasonForFailure()
	{
		return $this->reasonForFailure;
	}
	
}