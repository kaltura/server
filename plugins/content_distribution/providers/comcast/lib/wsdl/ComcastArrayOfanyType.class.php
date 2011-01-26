<?php


class ComcastArrayOfanyType extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("anyType");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


