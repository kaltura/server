<?php


class ComcastHinting extends SoapObject
{				
	const _NONE = 'None';
					
	const _OPTIMIZEFORSIZE = 'OptimizeForSize';
					
	const _OPTIMIZEFORSPEED = 'OptimizeForSpeed';
					
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


