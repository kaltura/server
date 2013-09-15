<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlSiteQueueType.class.php');
require_once(__DIR__ . '/WebexXmlServMatchingRecordsType.class.php');

class WebexXmlGetWebACDQueues extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlSiteQueueType>
	 */
	protected $queue;
	
	/**
	 *
	 * @var WebexXmlServMatchingRecordsType
	 */
	protected $matchingRecords;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'queue':
				return 'WebexXmlArray<WebexXmlSiteQueueType>';
	
			case 'matchingRecords':
				return 'WebexXmlServMatchingRecordsType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $queue
	 */
	public function getQueue()
	{
		return $this->queue;
	}
	
	/**
	 * @return WebexXmlServMatchingRecordsType $matchingRecords
	 */
	public function getMatchingRecords()
	{
		return $this->matchingRecords;
	}
	
}
		
