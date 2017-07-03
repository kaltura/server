<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlFailedToDeleteRecordings.class.php');

class WebexXmlDelRecordingFromRecycleBin extends WebexXmlObject
{
	/**
	 *
	 * @var int
	 */
	protected $successfulRecordingsCount;
	
	/**
	 * @var WebexXmlFailedToDeleteRecordings
	 */
	protected $failedRecordings;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'successfulRecordingsCount':
				return 'int';
			case'failedRecordings':
				return 'WebexXmlFailedToDeleteRecordings';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return int $successfulRecordingsCount
	 */
	public function getSuccessfulRecordingsCount()
	{
		return $this->successfulRecordingsCount;
	}
	
	/**
	 * @return WebexXmlFailedToDeleteRecordings $failedToDeleteRecordings
	 */
	public function getFailedRecordings()
	{
		return $this->failedRecordings;
	}
	
}
		
