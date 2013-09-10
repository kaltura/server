<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');

class WebexXmlCreateLab extends WebexXmlObject
{
	/**
	 *
	 * @var long
	 */
	protected $labID;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'labID':
				return 'long';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return long $labID
	 */
	public function getLabID()
	{
		return $this->labID;
	}
	
}

