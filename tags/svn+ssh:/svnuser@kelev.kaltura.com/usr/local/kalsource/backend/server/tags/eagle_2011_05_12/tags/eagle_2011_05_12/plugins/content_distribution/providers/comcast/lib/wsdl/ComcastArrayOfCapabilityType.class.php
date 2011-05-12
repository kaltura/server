<?php


class ComcastArrayOfCapabilityType extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastCapabilityType");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


