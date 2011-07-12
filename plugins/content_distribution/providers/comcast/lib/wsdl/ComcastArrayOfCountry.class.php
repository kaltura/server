<?php


class ComcastArrayOfCountry extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/base/:ArrayOfCountry';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastCountry");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


