<?php


class ComcastArrayOfCapability extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastCapability");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


