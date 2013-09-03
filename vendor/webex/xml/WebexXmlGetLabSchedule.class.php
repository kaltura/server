<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlTrainScheduleLabType.class.php');

class WebexXmlGetLabSchedule extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlTrainScheduleLabType>
	 */
	protected $scheduledLabs;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'scheduledLabs':
				return 'WebexXmlArray<WebexXmlTrainScheduleLabType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $scheduledLabs
	 */
	public function getScheduledLabs()
	{
		return $this->scheduledLabs;
	}
	
}
		
