<?php


class ComcastLicenseList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastLicense");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


