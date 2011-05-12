<?php


class ComcastArrayOfEndUserPermissionField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastEndUserPermissionField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


