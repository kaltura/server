<?php


class ComcastArrayOfDRMLicenseState extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastDRMLicenseState");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


