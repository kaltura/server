<?php


class ComcastHinting extends SoapObject
{				
	const _NONE = 'None';
					
	const _OPTIMIZEFORSIZE = 'OptimizeForSize';
					
	const _OPTIMIZEFORSPEED = 'OptimizeForSpeed';
					
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/enum/:Hinting';
	}
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


