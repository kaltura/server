<?php


class ComcastMediaFileType extends SoapObject
{				
	const _EXTERNAL = 'External';
					
	const _INTERNAL = 'Internal';
					
	public function getType()
	{
		return 'urn:theplatform-com:v4/content/enum/:MediaFileType';
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


