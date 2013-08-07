<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventRecordedEventsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $webExID;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlEventRecordedEventType>
	 */
	protected $recordedEvent;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'webExID':
				return 'string';
	
			case 'recordedEvent':
				return 'WebexXmlArray<WebexXmlEventRecordedEventType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'webExID',
			'recordedEvent',
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
		return 'recordedEventsType';
	}
	
	/**
	 * @param string $webExID
	 */
	public function setWebExID($webExID)
	{
		$this->webExID = $webExID;
	}
	
	/**
	 * @return string $webExID
	 */
	public function getWebExID()
	{
		return $this->webExID;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlEventRecordedEventType> $recordedEvent
	 */
	public function setRecordedEvent(WebexXmlArray $recordedEvent)
	{
		if($recordedEvent->getType() != 'WebexXmlEventRecordedEventType')
			throw new WebexXmlException(get_class($this) . "::recordedEvent must be of type WebexXmlEventRecordedEventType");
		
		$this->recordedEvent = $recordedEvent;
	}
	
	/**
	 * @return WebexXmlArray $recordedEvent
	 */
	public function getRecordedEvent()
	{
		return $this->recordedEvent;
	}
	
}
		
