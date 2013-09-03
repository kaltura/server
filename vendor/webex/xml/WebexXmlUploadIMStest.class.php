<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');

class WebexXmlUploadIMStest extends WebexXmlObject
{
	/**
	 *
	 * @var long
	 */
	protected $testID;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'testID':
				return 'long';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return long $testID
	 */
	public function getTestID()
	{
		return $this->testID;
	}
	
}

