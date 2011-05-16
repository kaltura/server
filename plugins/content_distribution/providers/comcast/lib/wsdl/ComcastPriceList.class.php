<?php


class ComcastPriceList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/rights/value/:PriceList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastPrice");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


