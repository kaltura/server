<?php


class ComcastArrayOfstring extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("string");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


