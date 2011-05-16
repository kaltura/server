<?php


class ComcastArrayOfPriceField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/rights/enum/:ArrayOfPriceField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastPriceField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


