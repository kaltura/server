<?php


class ComcastStorefrontList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/rights/value/:StorefrontList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastStorefront");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


