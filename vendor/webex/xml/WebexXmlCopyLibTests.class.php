<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/long.class.php');

class WebexXmlCopyLibTests extends WebexXmlObject
{
	/**
	 *
	 * @var WebexXmlArray<long>
	 */
	protected $testID;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'testID':
				return 'WebexXmlArray<long>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return WebexXmlArray $testID
	 */
	public function getTestID()
	{
		return $this->testID;
	}
	
}

