<?php


class ComcastEndUserPermissionList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastEndUserPermission");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


