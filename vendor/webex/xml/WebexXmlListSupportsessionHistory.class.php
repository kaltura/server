<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlHistorySupportSessionHistoryInstanceType.class.php');
require_once(__DIR__ . '/WebexXmlServMatchingRecordsType.class.php');

class WebexXmlListSupportsessionHistory extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlHistorySupportSessionHistoryInstanceType>
	 */
	protected $supportSessionHistory;
	
	/**
	 *
	 * @var WebexXmlServMatchingRecordsType
	 */
	protected $matchingRecords;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'supportSessionHistory':
				return 'WebexXmlArray<WebexXmlHistorySupportSessionHistoryInstanceType>';
	
			case 'matchingRecords':
				return 'WebexXmlServMatchingRecordsType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $supportSessionHistory
	 */
	public function getSupportSessionHistory()
	{
		return $this->supportSessionHistory;
	}
	
	/**
	 * @return WebexXmlServMatchingRecordsType $matchingRecords
	 */
	public function getMatchingRecords()
	{
		return $this->matchingRecords;
	}
	
}
		
