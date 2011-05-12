<?php


class ComcastPermissionList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastPermission");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


