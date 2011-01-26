<?php


class ComcastCapability extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'APIObject':
				return 'ComcastAPIObject';
			case 'capabilityType':
				return 'ComcastCapabilityType';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastAPIObject
	 **/
	public $APIObject;
				
	/**
	 * @var ComcastCapabilityType
	 **/
	public $capabilityType;
				
}


