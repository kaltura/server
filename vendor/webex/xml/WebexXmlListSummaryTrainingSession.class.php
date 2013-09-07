<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlTrainTrainingSessionSummaryInstanceType.class.php');
require_once(__DIR__ . '/WebexXmlServMatchingRecordsType.class.php');

class WebexXmlListSummaryTrainingSession extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlTrainTrainingSessionSummaryInstanceType>
	 */
	protected $trainingSession;
	
	/**
	 *
	 * @var WebexXmlServMatchingRecordsType
	 */
	protected $matchingRecords;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'trainingSession':
				return 'WebexXmlArray<WebexXmlTrainTrainingSessionSummaryInstanceType>';
	
			case 'matchingRecords':
				return 'WebexXmlServMatchingRecordsType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $trainingSession
	 */
	public function getTrainingSession()
	{
		return $this->trainingSession;
	}
	
	/**
	 * @return WebexXmlServMatchingRecordsType $matchingRecords
	 */
	public function getMatchingRecords()
	{
		return $this->matchingRecords;
	}
	
}

