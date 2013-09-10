<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlHistorySupportAttendeeHistoryInstanceType.class.php');
require_once(__DIR__ . '/WebexXmlServMatchingRecordsType.class.php');

class WebexXmlListSupportattendeeHistory extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlHistorySupportAttendeeHistoryInstanceType>
	 */
	protected $supportAttendeeHistory;
	
	/**
	 *
	 * @var WebexXmlServMatchingRecordsType
	 */
	protected $matchingRecords;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'supportAttendeeHistory':
				return 'WebexXmlArray<WebexXmlHistorySupportAttendeeHistoryInstanceType>';
	
			case 'matchingRecords':
				return 'WebexXmlServMatchingRecordsType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $supportAttendeeHistory
	 */
	public function getSupportAttendeeHistory()
	{
		return $this->supportAttendeeHistory;
	}
	
	/**
	 * @return WebexXmlServMatchingRecordsType $matchingRecords
	 */
	public function getMatchingRecords()
	{
		return $this->matchingRecords;
	}
	
}
		
