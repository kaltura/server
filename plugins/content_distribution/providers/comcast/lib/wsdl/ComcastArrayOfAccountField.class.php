<?php


class ComcastArrayOfAccountField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastAccountField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


