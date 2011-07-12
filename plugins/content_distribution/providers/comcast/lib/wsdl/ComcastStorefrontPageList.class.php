<?php


class ComcastStorefrontPageList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/rights/value/:StorefrontPageList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastStorefrontPage");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


