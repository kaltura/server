<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlServMatchingRecordsType.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlHistorySalesAttendeeHistoryInstanceType.class.php');

class WebexXmlListSalesAttendeeHistory extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlServMatchingRecordsType
	 */
	protected $matchingRecords;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlHistorySalesAttendeeHistoryInstanceType>
	 */
	protected $salesAttendeeHistory;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'matchingRecords':
				return 'WebexXmlServMatchingRecordsType';
	
			case 'salesAttendeeHistory':
				return 'WebexXmlArray<WebexXmlHistorySalesAttendeeHistoryInstanceType>';
	
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
	 * @return WebexXmlArray $salesAttendeeHistory
	 */
	public function getSalesAttendeeHistory()
	{
		return $this->salesAttendeeHistory;
	}
	
}
		
