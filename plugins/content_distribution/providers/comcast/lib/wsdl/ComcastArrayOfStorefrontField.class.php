<?php


class ComcastArrayOfStorefrontField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/rights/enum/:ArrayOfStorefrontField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastStorefrontField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


