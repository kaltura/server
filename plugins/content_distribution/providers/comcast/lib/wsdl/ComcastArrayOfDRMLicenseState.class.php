<?php


class ComcastArrayOfDRMLicenseState extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/rights/value/:ArrayOfDRMLicenseState';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastDRMLicenseState");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


