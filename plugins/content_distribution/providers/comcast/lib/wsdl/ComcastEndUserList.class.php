<?php


class ComcastEndUserList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/rights/value/:EndUserList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastEndUser");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


