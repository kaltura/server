<?php


class ComcastUsagePlanList extends SoapArray
{				
	public function getType()
	{
		return 'urn:theplatform-com:v4/rights/value/:UsagePlanList';
	}
				
	public function __construct()
	{
		parent::__construct("ComcastUsagePlan");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


