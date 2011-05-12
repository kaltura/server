<?php


class ComcastArrayOfStorefrontOrder extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastStorefrontOrder");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


