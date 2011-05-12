<?php


class ComcastArrayOfServerField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastServerField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


