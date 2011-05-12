<?php


class ComcastArrayOfPermissionField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastPermissionField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


