<?php


class ComcastContactInfo extends SoapObject
{				
	const _HIDDEN = 'Hidden';
					
	const _OPTIONAL = 'Optional';
					
	const _REQUIRED = 'Required';
					
	public function getType()
	{
		return 'urn:theplatform-com:v4/enum/:ContactInfo';
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


