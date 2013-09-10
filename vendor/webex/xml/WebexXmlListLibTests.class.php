<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlTrainLibTestInstanceType.class.php');

class WebexXmlListLibTests extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlTrainLibTestInstanceType>
	 */
	protected $libTest;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'libTest':
				return 'WebexXmlArray<WebexXmlTrainLibTestInstanceType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $libTest
	 */
	public function getLibTest()
	{
		return $this->libTest;
	}
	
}
		
