<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlHistoryTrainSessionHistoryInstanceType.class.php');
require_once(__DIR__ . '/WebexXmlServMatchingRecordsType.class.php');

class WebexXmlListTrainingsessionHistory extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlHistoryTrainSessionHistoryInstanceType>
	 */
	protected $trainingSessionHistory;
	
	/**
	 *
	 * @var WebexXmlServMatchingRecordsType
	 */
	protected $matchingRecords;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'trainingSessionHistory':
				return 'WebexXmlArray<WebexXmlHistoryTrainSessionHistoryInstanceType>';
	
			case 'matchingRecords':
				return 'WebexXmlServMatchingRecordsType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $trainingSessionHistory
	 */
	public function getTrainingSessionHistory()
	{
		return $this->trainingSessionHistory;
	}
	
	/**
	 * @return WebexXmlServMatchingRecordsType $matchingRecords
	 */
	public function getMatchingRecords()
	{
		return $this->matchingRecords;
	}
	
}
		
