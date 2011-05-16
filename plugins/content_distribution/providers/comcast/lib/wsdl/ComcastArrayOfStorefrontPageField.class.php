<?php


class ComcastArrayOfStorefrontPageField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/rights/enum/:ArrayOfStorefrontPageField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastStorefrontPageField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


