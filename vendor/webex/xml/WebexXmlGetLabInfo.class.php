<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlTrainLabInfoType.class.php');

class WebexXmlGetLabInfo extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlTrainLabInfoType>
	 */
	protected $labInfo;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'labInfo':
				return 'WebexXmlArray<WebexXmlTrainLabInfoType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $labInfo
	 */
	public function getLabInfo()
	{
		return $this->labInfo;
	}
	
}

