<?php


class ComcastArrayOfRoleField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastRoleField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


