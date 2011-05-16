<?php


class ComcastArrayOfLicenseField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/rights/enum/:ArrayOfLicenseField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastLicenseField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


