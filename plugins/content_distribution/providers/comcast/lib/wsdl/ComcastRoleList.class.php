<?php


class ComcastRoleList extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastRole");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


