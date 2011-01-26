<?php


class ComcastArrayOfCountry extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastCountry");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


