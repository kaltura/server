<?php


class ComcastArrayOfCapabilityType extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/admin/enum/:ArrayOfCapabilityType';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastCapabilityType");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


