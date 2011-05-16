<?php


class ComcastPortalList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/rights/value/:PortalList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastPortal");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


