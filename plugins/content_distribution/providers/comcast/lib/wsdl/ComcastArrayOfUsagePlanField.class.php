<?php


class ComcastArrayOfUsagePlanField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastUsagePlanField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


