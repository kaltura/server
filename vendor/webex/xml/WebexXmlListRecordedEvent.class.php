<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlServMatchingRecordsType.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlEventRecordedEventsType.class.php');

class WebexXmlListRecordedEvent extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlServMatchingRecordsType
	 */
	protected $matchingRecords;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlEventRecordedEventsType>
	 */
	protected $event;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'matchingRecords':
				return 'WebexXmlServMatchingRecordsType';
	
			case 'event':
				return 'WebexXmlArray<WebexXmlEventRecordedEventsType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlServMatchingRecordsType $matchingRecords
	 */
	public function getMatchingRecords()
	{
		return $this->matchingRecords;
	}
	
	/**
	 * @return WebexXmlArray $event
	 */
	public function getEvent()
	{
		return $this->event;
	}
	
}
		
