<?php


class ComcastArrayOfMediaField extends SoapArray
{				
	public function __construct()
	{
		parent::__construct("ComcastMediaField");	
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


