<?php


class ComcastExpression extends SoapObject
{				
	const _FULL = 'Full';
					
	const _NONSTOP = 'NonStop';
					
	const _SAMPLE = 'Sample';
					
	const _UNKNOWN = 'Unknown';
					
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/enum/:Expression';
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


