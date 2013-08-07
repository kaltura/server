<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlTrainScheduledTestType.class.php');

class WebexXmlGetTrainingSession extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlTrainScheduledTestType>
	 */
	protected $test;
	
	/**
	 *
	 * @var string
	 */
	protected $hostKey;
	
	/**
	 *
	 * @var long
	 */
	protected $eventID;
	
	/**
	 *
	 * @var string
	 */
	protected $guestToken;
	
	/**
	 *
	 * @var string
	 */
	protected $hostType;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'test':
				return 'WebexXmlArray<WebexXmlTrainScheduledTestType>';
	
			case 'hostKey':
				return 'string';
	
			case 'eventID':
				return 'long';
	
			case 'guestToken':
				return 'string';
	
			case 'hostType':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $test
	 */
	public function getTest()
	{
		return $this->test;
	}
	
	/**
	 * @return string $hostKey
	 */
	public function getHostKey()
	{
		return $this->hostKey;
	}
	
	/**
	 * @return long $eventID
	 */
	public function getEventID()
	{
		return $this->eventID;
	}
	
	/**
	 * @return string $guestToken
	 */
	public function getGuestToken()
	{
		return $this->guestToken;
	}
	
	/**
	 * @return string $hostType
	 */
	public function getHostType()
	{
		return $this->hostType;
	}
	
}
		
