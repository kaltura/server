<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlHistoryMeetingAttendeeHistoryInstanceType.class.php');
require_once(__DIR__ . '/WebexXmlServMatchingRecordsType.class.php');

class WebexXmlListMeetingattendeeHistory extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlHistoryMeetingAttendeeHistoryInstanceType>
	 */
	protected $meetingAttendeeHistory;
	
	/**
	 *
	 * @var WebexXmlServMatchingRecordsType
	 */
	protected $matchingRecords;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'meetingAttendeeHistory':
				return 'WebexXmlArray<WebexXmlHistoryMeetingAttendeeHistoryInstanceType>';
	
			case 'matchingRecords':
				return 'WebexXmlServMatchingRecordsType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $meetingAttendeeHistory
	 */
	public function getMeetingAttendeeHistory()
	{
		return $this->meetingAttendeeHistory;
	}
	
	/**
	 * @return WebexXmlServMatchingRecordsType $matchingRecords
	 */
	public function getMatchingRecords()
	{
		return $this->matchingRecords;
	}
	
}
		
