<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlHistoryRecordAccessHistoryInstanceType.class.php');
require_once(__DIR__ . '/WebexXmlServMatchingRecordsType.class.php');

class WebexXmlListRecordaccessHistory extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlHistoryRecordAccessHistoryInstanceType>
	 */
	protected $recordAccessHistory;
	
	/**
	 *
	 * @var WebexXmlServMatchingRecordsType
	 */
	protected $matchingRecords;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'recordAccessHistory':
				return 'WebexXmlArray<WebexXmlHistoryRecordAccessHistoryInstanceType>';
	
			case 'matchingRecords':
				return 'WebexXmlServMatchingRecordsType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $recordAccessHistory
	 */
	public function getRecordAccessHistory()
	{
		return $this->recordAccessHistory;
	}
	
	/**
	 * @return WebexXmlServMatchingRecordsType $matchingRecords
	 */
	public function getMatchingRecords()
	{
		return $this->matchingRecords;
	}
	
}

