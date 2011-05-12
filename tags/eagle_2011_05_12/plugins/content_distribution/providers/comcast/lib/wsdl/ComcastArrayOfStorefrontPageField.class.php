<?php


class ComcastArrayOfStorefrontPageField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastStorefrontPageField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


