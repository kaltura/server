<?php


class ComcastUsagePlanList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastUsagePlan");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


