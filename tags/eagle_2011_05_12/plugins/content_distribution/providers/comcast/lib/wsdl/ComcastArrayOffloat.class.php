<?php


class ComcastArrayOffloat extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("float");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


