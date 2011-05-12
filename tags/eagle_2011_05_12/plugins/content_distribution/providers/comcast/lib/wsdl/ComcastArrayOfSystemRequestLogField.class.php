<?php


class ComcastArrayOfSystemRequestLogField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastSystemRequestLogField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


