<?php


class ComcastArrayOfAdminView extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastAdminView");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


