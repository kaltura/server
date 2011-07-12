<?php


class ComcastArrayOfCapability extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/value/:ArrayOfCapability';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastCapability");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


