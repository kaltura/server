<?php


class ComcastArrayOfEndUserPermissionField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/rights/enum/:ArrayOfEndUserPermissionField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastEndUserPermissionField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


