<?php


class ComcastArrayOfUsagePlanField extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/rights/enum/:ArrayOfUsagePlanField';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastUsagePlanField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


