<?php


class ComcastArrayOfLocationField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastLocationField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


