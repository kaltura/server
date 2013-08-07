<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');

class WebexXmlGetSetupComputerURL extends WebexXmlObject
{
	/**
	 *
	 * @var string
	 */
	protected $SetupComputerURL;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'SetupComputerURL':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return string $SetupComputerURL
	 */
	public function getSetupComputerURL()
	{
		return $this->SetupComputerURL;
	}
	
}

