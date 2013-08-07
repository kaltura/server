<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlTrainScheduledTestInstanceType.class.php');

class WebexXmlListScheduledTests extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlTrainScheduledTestInstanceType>
	 */
	protected $test;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'test':
				return 'WebexXmlArray<WebexXmlTrainScheduledTestInstanceType>';
	
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
	
}

