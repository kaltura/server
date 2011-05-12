<?php


class ComcastArrayOfPortalField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastPortalField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


