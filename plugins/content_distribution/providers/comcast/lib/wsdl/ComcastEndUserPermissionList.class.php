<?php


class ComcastEndUserPermissionList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/rights/value/:EndUserPermissionList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastEndUserPermission");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


