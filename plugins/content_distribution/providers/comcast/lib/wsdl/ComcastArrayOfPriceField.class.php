<?php


class ComcastArrayOfPriceField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastPriceField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


