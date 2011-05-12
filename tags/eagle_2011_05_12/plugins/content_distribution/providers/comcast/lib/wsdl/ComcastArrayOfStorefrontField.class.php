<?php


class ComcastArrayOfStorefrontField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastStorefrontField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


