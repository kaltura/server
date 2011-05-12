<?php


class ComcastArrayOfLicenseField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastLicenseField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


