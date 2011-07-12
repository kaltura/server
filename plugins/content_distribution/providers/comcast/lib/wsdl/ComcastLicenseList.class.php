<?php


class ComcastLicenseList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/rights/value/:LicenseList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastLicense");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


